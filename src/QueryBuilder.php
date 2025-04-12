<?php

declare(strict_types=1);

namespace MySaasPackage\QueryBuilder;

class QueryBuilder
{
    private string $table = '';
    private string $tableAlias = '';
    private array $selectClauses = [];
    private array $whereClauses = [];
    private array $joinClauses = [];
    private array $groupByClauses = [];
    private array $havingClauses = [];
    private array $orderByClauses = [];
    private array $params = [];
    private array $ctes = [];
    private ?int $limitValue = null;
    private ?int $offsetValue = null;
    private array $values = [];
    private string $operation = 'select';

    public function __construct(?string $table = null)
    {
        if ($table) {
            $this->table = $table;
        }
    }

    public function select(...$columns): self
    {
        if (empty($columns)) {
            $this->selectClauses = ['*'];
            return $this;
        }

        // If first argument is an array, use it directly
        if (is_array($columns[0])) {
            $this->selectClauses = $columns[0];
        } else {
            $this->selectClauses = $columns;
        }

        return $this;
    }

    public function from(string $table, ?string $alias = null): self
    {
        $this->table = $table;
        $this->tableAlias = $alias ?? '';

        return $this;
    }

    public function where(string $condition, array $params = []): self
    {
        if (!empty($this->whereClauses)) {
            $condition = "AND {$condition}";
        }

        // Replace ? with :n parameters
        $qCount = substr_count($condition, '?');
        if ($qCount > 0) {
            $offset = count($this->params);
            for ($i = 0; $i < $qCount; $i++) {
                $pos = strpos($condition, '?');
                $paramName = (string)($offset + $i);
                
                // Check if this is an IN clause
                $inClauseCheck = substr($condition, max(0, $pos - 4), 4);
                if (strtoupper($inClauseCheck) === ' IN ') {
                    $condition = substr_replace($condition, "(:{$paramName})", $pos, 1);
                } else {
                    $condition = substr_replace($condition, ":{$paramName}", $pos, 1);
                }
                
                if (isset($params[$i])) {
                    $this->params[$paramName] = $params[$i];
                }
            }
            $params = []; // Clear params as they've been processed
        }

        foreach ($params as $key => $value) {
            if ($value instanceof QueryBuilder) {
                $condition = str_replace(":$key", $this->formatValue($value), $condition);
                $this->params = array_merge($this->params, $value->getParams());
            } elseif (is_array($value)) {
                $this->params[$key] = $value;
                // Check if this is an IN clause
                if (strpos($condition, " IN :$key") !== false) {
                    $condition = str_replace(" IN :$key", " IN (:$key)", $condition);
                }
            } else {
                if (is_int($key)) {
                    $key = (string)$key;
                }
                $this->params[$key] = $value;
            }
        }

        $this->whereClauses[] = $condition;
        return $this;
    }

    public function andWhere(string $condition, array $params = []): self
    {
        return $this->where($condition, $params);
    }

    public function orWhere(
        string $condition,
        array $params = []
    ): self {
        if (!empty($this->whereClauses)) {
            $condition = "OR {$condition}";
        }

        $this->whereClauses[] = $condition;
        $this->params = array_merge($this->params, $params);

        return $this;
    }

    public function join(
        string $table,
        string $alias,
        string $condition
    ): self {
        $this->joinClauses[] = "JOIN {$table} AS {$alias} ON {$condition}";

        return $this;
    }

    public function leftJoin(
        string $table,
        string $alias,
        string $condition
    ): self {
        $this->joinClauses[] = "LEFT JOIN {$table} AS {$alias} ON {$condition}";

        return $this;
    }

    public function rightJoin(
        string $table,
        string $alias,
        string $condition
    ): self {
        $this->joinClauses[] = "RIGHT JOIN {$table} AS {$alias} ON {$condition}";

        return $this;
    }

    public function groupBy(string ...$columns): self
    {
        $this->groupByClauses = array_merge($this->groupByClauses, $columns);

        return $this;
    }

    public function having(
        string $condition,
        array $params = []
    ): self {
        $this->havingClauses[] = $condition;
        $this->params = array_merge($this->params, $params);

        return $this;
    }

    public function orderBy(string $column, ?string $direction = null): self
    {
        $this->orderByClauses[] = $direction ? "{$column} {$direction}" : $column;
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limitValue = $limit;

        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offsetValue = $offset;

        return $this;
    }

    public function with(
        string $name,
        QueryBuilder $query,
        array $columns = [],
        bool $recursive = false
    ): self {
        $this->ctes[] = [
            'name' => $name,
            'query' => $query,
            'columns' => $columns,
            'recursive' => $recursive,
            'union' => false
        ];

        // Merge parameters from the CTE query
        $this->params = array_merge($this->params, $query->getParams());

        return $this;
    }

    public function withRecursive(
        string $name,
        QueryBuilder $baseQuery,
        QueryBuilder $recursiveQuery,
        array $columns = []
    ): self {
        // Add the anchor part
        $this->ctes[] = [
            'name' => $name,
            'query' => $baseQuery,
            'columns' => $columns,
            'recursive' => true,
            'union' => false
        ];

        // Add the recursive part
        $this->ctes[] = [
            'name' => $name,
            'query' => $recursiveQuery,
            'columns' => $columns,
            'recursive' => true,
            'union' => true
        ];

        $this->params = array_merge($this->params, $baseQuery->getParams(), $recursiveQuery->getParams());

        return $this;
    }

    private function buildCTEs(): string
    {
        if (empty($this->ctes)) {
            return '';
        }

        $isRecursive = false;
        $cteGroups = [];
        $currentGroup = null;

        foreach ($this->ctes as $cte) {
            if ($cte['recursive']) {
                $isRecursive = true;
            }

            if ($currentGroup === null || $currentGroup['name'] !== $cte['name']) {
                if ($currentGroup !== null) {
                    $cteGroups[] = $this->buildCTEGroup($currentGroup);
                }
                $currentGroup = [
                    'name' => $cte['name'],
                    'parts' => []
                ];
            }
            $currentGroup['parts'][] = $cte;
        }

        if ($currentGroup !== null) {
            $cteGroups[] = $this->buildCTEGroup($currentGroup);
        }

        $prefix = $isRecursive ? 'WITH RECURSIVE ' : 'WITH ';

        return $prefix . implode(', ', $cteGroups);
    }

    private function buildCTEGroup(array $group): string
    {
        $parts = [];
        foreach ($group['parts'] as $index => $cte) {
            $columnDef = !empty($cte['columns']) ? '(' . implode(', ', $cte['columns']) . ')' : '';
            $sql = $cte['query']->toSQL();
            $parts[] = $index === 0 ? $sql : 'UNION ALL ' . $sql;
        }

        return $group['name'] . $columnDef . ' AS (' . implode(' ', $parts) . ')';
    }

    public function toSQL(): string
    {
        return match($this->operation) {
            'insert' => $this->buildInsertSQL(),
            'update' => $this->buildUpdateSQL(),
            'delete' => $this->buildDeleteSQL(),
            default => $this->buildSelectSQL(),
        };
    }

    private function buildSelectSQL(): string
    {
        $sql = [];

        // Add CTEs if present
        $ctes = $this->buildCTEs();
        if ($ctes) {
            $sql[] = $ctes;
        }

        // Build SELECT clause
        $sql[] = 'SELECT ' . implode(', ', $this->selectClauses);

        // Build FROM clause
        $sql[] = 'FROM ' . $this->table . ($this->tableAlias ? " AS {$this->tableAlias}" : '');

        // Add JOINs if present
        if (!empty($this->joinClauses)) {
            $sql[] = implode(' ', $this->joinClauses);
        }

        // Add WHERE clause if present
        if (!empty($this->whereClauses)) {
            $sql[] = 'WHERE ' . implode(' ', $this->whereClauses);
        }

        // Add GROUP BY clause if present
        if (!empty($this->groupByClauses)) {
            $sql[] = 'GROUP BY ' . implode(', ', $this->groupByClauses);
        }

        // Add HAVING clause if present
        if (!empty($this->havingClauses)) {
            $sql[] = 'HAVING ' . implode(' AND ', $this->havingClauses);
        }

        // Add ORDER BY clause if present
        if (!empty($this->orderByClauses)) {
            $sql[] = 'ORDER BY ' . implode(', ', $this->orderByClauses);
        }

        // Add LIMIT and OFFSET if present
        if ($this->limitValue !== null) {
            $sql[] = "LIMIT {$this->limitValue}";
        }

        if ($this->offsetValue !== null) {
            $sql[] = "OFFSET {$this->offsetValue}";
        }

        return implode(' ', $sql);
    }

    private function buildInsertSQL(): string
    {
        $columns = array_keys($this->values);
        $values = array_values($this->values);

        return sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            implode(', ', $values)
        );
    }

    private function buildUpdateSQL(): string
    {
        $sets = array_map(
            fn($column, $value) => "{$column} = {$value}",
            array_keys($this->values),
            array_values($this->values)
        );

        $sql = sprintf('UPDATE %s SET %s', $this->table, implode(', ', $sets));

        if (!empty($this->whereClauses)) {
            $sql .= ' WHERE ' . implode(' ', $this->whereClauses);
        }

        return $sql;
    }

    private function buildDeleteSQL(): string
    {
        $sql = "DELETE FROM {$this->table}";

        if (!empty($this->whereClauses)) {
            $sql .= ' WHERE ' . implode(' ', $this->whereClauses);
        }

        return $sql;
    }

    private function formatValue($value): string
    {
        if ($value instanceof QueryBuilder) {
            return '(' . $value->toSQL() . ')';
        }

        if (is_array($value)) {
            return ':' . array_key_first($this->params);
        }

        return $value;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function setParameter(string|int $key, mixed $value): self
    {
        if (is_int($key)) {
            $key = (string)$key;
        }
        $this->params[$key] = $value;
        return $this;
    }

    public function insert(string $table): self
    {
        $this->operation = 'insert';
        $this->table = $table;
        return $this;
    }

    public function values(array $values): self
    {
        $this->values = $values;
        return $this;
    }

    public function update(string $table): self
    {
        $this->operation = 'update';
        $this->table = $table;
        return $this;
    }

    public function set(array $values): self
    {
        $this->values = $values;
        return $this;
    }

    public function delete(string $table): self
    {
        $this->operation = 'delete';
        $this->table = $table;
        return $this;
    }
}

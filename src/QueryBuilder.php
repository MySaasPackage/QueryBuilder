<?php

declare(strict_types=1);

namespace MySaasPackage\QueryBuilder;

class QueryBuilder
{
    private string $table = '';
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

    public function __construct(?string $table = null)
    {
        if ($table) {
            $this->table = $table;
        }
    }

    public function select(string ...$columns): self
    {
        $this->selectClauses = $columns ?: ['*'];

        return $this;
    }

    public function from(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    public function where(string $condition, array $params = []): self
    {
        if (!empty($this->whereClauses)) {
            $condition = "AND {$condition}";
        }

        $this->whereClauses[] = $condition;
        $this->params = array_merge($this->params, $params);

        return $this;
    }

    public function andWhere(string $condition, array $params = []): self
    {
        return $this->where($condition, $params);
    }

    public function orWhere(string $condition, array $params = []): self
    {
        if (!empty($this->whereClauses)) {
            $condition = "OR {$condition}";
        }

        $this->whereClauses[] = $condition;
        $this->params = array_merge($this->params, $params);

        return $this;
    }

    public function join(string $table, string $condition): self
    {
        $this->joinClauses[] = "JOIN {$table} ON {$condition}";

        return $this;
    }

    public function leftJoin(string $table, string $condition): self
    {
        $this->joinClauses[] = "LEFT JOIN {$table} ON {$condition}";

        return $this;
    }

    public function rightJoin(string $table, string $condition): self
    {
        $this->joinClauses[] = "RIGHT JOIN {$table} ON {$condition}";

        return $this;
    }

    public function groupBy(string ...$columns): self
    {
        $this->groupByClauses = array_merge($this->groupByClauses, $columns);

        return $this;
    }

    public function having(string $condition, array $params = []): self
    {
        $this->havingClauses[] = $condition;
        $this->params = array_merge($this->params, $params);

        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderByClauses[] = "{$column} {$direction}";

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

    public function with(string $name, QueryBuilder $query, array $columns = [], bool $recursive = false): self
    {
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

    public function withRecursive(string $name, QueryBuilder $query, array $columns = []): self
    {
        return $this->with($name, $query, $columns, true);
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
        $parts = [];

        // Add CTEs if present
        $cteSQL = $this->buildCTEs();
        if ($cteSQL) {
            $parts[] = $cteSQL;
        }

        // Build the main query
        $parts[] = 'SELECT ' . implode(', ', $this->selectClauses);
        $parts[] = 'FROM ' . $this->table;

        if (!empty($this->joinClauses)) {
            $parts[] = implode(' ', $this->joinClauses);
        }

        if (!empty($this->whereClauses)) {
            $parts[] = 'WHERE ' . implode(' ', $this->whereClauses);
        }

        if (!empty($this->groupByClauses)) {
            $parts[] = 'GROUP BY ' . implode(', ', $this->groupByClauses);
        }

        if (!empty($this->havingClauses)) {
            $parts[] = 'HAVING ' . implode(' AND ', $this->havingClauses);
        }

        if (!empty($this->orderByClauses)) {
            $parts[] = 'ORDER BY ' . implode(', ', $this->orderByClauses);
        }

        if (null !== $this->limitValue) {
            $parts[] = 'LIMIT ' . $this->limitValue;
        }

        if (null !== $this->offsetValue) {
            $parts[] = 'OFFSET ' . $this->offsetValue;
        }

        return implode(' ', $parts);
    }

    public function getParams(): array
    {
        return $this->params;
    }
}

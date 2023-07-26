<?php

declare(strict_types=1);

namespace MySaasPackage\Support;

use RuntimeException;
use MySaasPackage\Support\QueryPart\Join;
use MySaasPackage\Support\QueryPart\Part;
use MySaasPackage\Support\QueryPart\Where;
use MySaasPackage\Support\QueryPart\CtePart;
use MySaasPackage\Support\QueryPart\OrderBy;
use MySaasPackage\Support\QueryPart\JoinPart;
use MySaasPackage\Support\QueryPart\LimitPart;
use MySaasPackage\Support\QueryPart\TablePart;
use MySaasPackage\Support\QueryPart\WherePart;
use MySaasPackage\Support\QueryPart\ValuesPart;
use MySaasPackage\Support\QueryPart\ColumnsPart;
use MySaasPackage\Support\QueryPart\GroupByPart;
use MySaasPackage\Support\QueryPart\OrderByPart;
use MySaasPackage\Support\QueryPart\HavingByPart;
use MySaasPackage\Support\QueryPart\ParameterPart;
use MySaasPackage\Support\QueryPart\ReturningPart;
use MySaasPackage\Support\QueryPart\CtePartCollection;
use MySaasPackage\Support\QueryPart\JoinPartCollection;
use MySaasPackage\Support\QueryPart\UpdateSetValuesPart;
use MySaasPackage\Support\QueryPart\WherePartCollection;
use MySaasPackage\Support\QueryPart\OrderByPartCollection;
use MySaasPackage\Support\QueryPart\ParameterPartCollection;

class QueryBuilder implements Part
{
    protected array $parts = [];

    public const TYPE = 'type';
    public const SELECT = 'select';
    public const INSERT = 'insert';
    public const UPDATE = 'update';
    public const DELETE = 'delete';

    public function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }

    public function select(array $columns = ['*']): self
    {
        $this->parts[self::TYPE] = self::SELECT;
        $this->parts[ColumnsPart::class] = ColumnsPart::fromRawArray($columns);

        return $this;
    }

    public function insert(string $table): self
    {
        $this->parts[self::TYPE] = self::INSERT;
        $this->parts[TablePart::class] = new TablePart($table);

        return $this;
    }

    public function into(string $table): self
    {
        $this->parts[TablePart::class] = new TablePart($table);

        return $this;
    }

    public function values(array $values = []): self
    {
        if (self::INSERT !== $this->parts[self::TYPE]) {
            throw new RuntimeException('You can only use values() with insert()');
        }

        $this->parts[ColumnsPart::class] = ColumnsPart::fromRawArray(array_keys($values));
        $this->parts[ValuesPart::class] = new ValuesPart(array_values($values));

        return $this;
    }

    public function update(string $table): self
    {
        $this->parts[self::TYPE] = self::UPDATE;
        $this->parts[TablePart::class] = new TablePart($table);

        return $this;
    }

    public function set(array $values = []): self
    {
        if (self::UPDATE !== $this->parts[self::TYPE]) {
            throw new RuntimeException('You can only use set() with update()');
        }

        $this->parts[UpdateSetValuesPart::class] = new UpdateSetValuesPart($values);

        return $this;
    }

    public function delete(): self
    {
        $this->parts[self::TYPE] = self::DELETE;

        return $this;
    }

    public function table(string $table, string $alias = null): self
    {
        $this->parts[TablePart::class] = new TablePart($table, $alias);

        return $this;
    }

    public function from(string $table, string $alias = null): self
    {
        $this->parts[TablePart::class] = new TablePart($table, $alias);

        return $this;
    }

    protected function addParam(ParameterPart $param): self
    {
        $this->parts[ParameterPartCollection::class] ??= new ParameterPartCollection();
        $this->parts[ParameterPartCollection::class]->add($param);

        return $this;
    }

    public function setParameter(string|int $key, $value): self
    {
        $this->addParam(new ParameterPart($key, $value));

        return $this;
    }

    protected function addWhere(WherePart $wherePart): self
    {
        $this->parts[WherePartCollection::class] ??= new WherePartCollection();
        $this->parts[WherePartCollection::class]->add($wherePart);

        return $this;
    }

    public function where(string $condition): self
    {
        $this->addWhere(new WherePart($condition));

        return $this;
    }

    public function andWhere(string $condition): self
    {
        $this->addWhere(new WherePart($condition, Where::AND));

        return $this;
    }

    public function orWhere(string $condition): self
    {
        $this->addWhere(new WherePart($condition, Where::Or));

        return $this;
    }

    public function returning(array $columns = []): self
    {
        $this->parts[ReturningPart::class] = new ReturningPart($columns);

        return $this;
    }

    protected function addJoin(JoinPart $join): self
    {
        $this->parts[JoinPartCollection::class] ??= new JoinPartCollection();
        $this->parts[JoinPartCollection::class]->add($join);

        return $this;
    }

    public function join(string $table, string $alias, string $condition): self
    {
        $this->addJoin(new JoinPart(
            type: Join::JOIN,
            table: new TablePart($table, $alias),
            condition: $condition
        ));

        return $this;
    }

    public function leftJoin(string $table, string $alias, string $condition): self
    {
        $this->addJoin(new JoinPart(
            type: Join::LEFT_JOIN,
            table: new TablePart($table, $alias),
            condition: $condition
        ));

        return $this;
    }

    public function rightJoin(string $table, string $alias, string $condition): self
    {
        $this->addJoin(new JoinPart(
            type: Join::RIGHT_JOIN,
            table: new TablePart($table, $alias),
            condition: $condition
        ));

        return $this;
    }

    public function innerJoin(string $table, string $alias, string $condition): self
    {
        $this->addJoin(new JoinPart(
            type: Join::INNER_JOIN,
            table: new TablePart($table, $alias),
            condition: $condition
        ));

        return $this;
    }

    protected function addOrderBy(OrderByPart $orderBy): self
    {
        $this->parts[OrderByPartCollection::class] ??= new OrderByPartCollection();
        $this->parts[OrderByPartCollection::class]->add($orderBy);

        return $this;
    }

    public function orderBy(array $columns, OrderBy|string $direction = null): self
    {
        $direction ??= OrderBy::ASC;

        if (is_string($direction)) {
            $direction = OrderBy::from($direction);
        }

        $this->addOrderBy(new OrderByPart($columns, $direction));

        return $this;
    }

    public function groupBy($columns): self
    {
        $this->parts[GroupByPart::class] = new GroupByPart($columns);

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->parts[LimitPart::class] = new LimitPart($limit);

        return $this;
    }

    public function having(string $condition): self
    {
        $this->parts[HavingByPart::class] = new HavingByPart($condition);

        return $this;
    }

    protected function addCte(CtePart $cte): self
    {
        $this->parts[CtePartCollection::class] ??= new CtePartCollection();
        $this->parts[CtePartCollection::class]->add($cte);

        return $this;
    }

    public function with(string $alias, QueryBuilder $query): self
    {
        $this->addCte(new CtePart(alias: $alias, query: $query));

        return $this;
    }

    protected function bindParameterParts(string $sql): string
    {
        if (!isset($this->parts[ParameterPartCollection::class])) {
            return $sql;
        }

        $params = $this->parts[ParameterPartCollection::class]->params;

        $patterns = [];
        $replacements = [];

        foreach ($params as $param) {
            if (is_int($param->key)) {
                $patterns[] = '/\?/';
            } else {
                $patterns[] = sprintf('/:%s/', $param->key);
            }

            $replacements[] = strval($param->value);
        }

        return preg_replace($patterns, $replacements, $sql, 1);
    }

    public function __toSelect(): string
    {
        $table = $this->parts[TablePart::class]->__toString();
        $columns = $this->parts[ColumnsPart::class]->__toString();

        $sql = "SELECT {$columns} FROM {$table}";

        if (isset($this->parts[CtePartCollection::class]) && $this->parts[CtePartCollection::class]->isNotEmpty()) {
            $ctes = $this->parts[CtePartCollection::class]->__toString();
            $sql = "{$ctes} {$sql}";
        }

        if (isset($this->parts[JoinPartCollection::class]) && $this->parts[JoinPartCollection::class]->isNotEmpty()) {
            $join = $this->parts[JoinPartCollection::class]->__toString();
            $sql = "{$sql} {$join}";
        }

        if (isset($this->parts[WherePartCollection::class]) && $this->parts[WherePartCollection::class]->isNotEmpty()) {
            $wheres = $this->parts[WherePartCollection::class]->__toString();
            $sql = "{$sql} {$wheres}";
        }

        if (isset($this->parts[OrderByPartCollection::class]) && $this->parts[OrderByPartCollection::class]->isNotEmpty()) {
            $orderBy = $this->parts[OrderByPartCollection::class]->__toString();
            $sql = "{$sql} {$orderBy}";
        }

        if (isset($this->parts[GroupByPart::class])) {
            $groupBy = $this->parts[GroupByPart::class]->__toString();
            $sql = "{$sql} {$groupBy}";
        }

        if (isset($this->parts[HavingByPart::class])) {
            $harving = $this->parts[HavingByPart::class]->__toString();
            $sql = "{$sql} {$harving}";
        }

        if (isset($this->parts[LimitPart::class])) {
            $limit = $this->parts[LimitPart::class]->__toString();
            $sql = "{$sql} {$limit}";
        }

        if (isset($this->parts[ReturningPart::class])) {
            $returning = $this->parts[ReturningPart::class]->__toString();
            $sql = "{$sql} {$returning}";
        }

        return $sql;
    }

    public function __toInsert(): string
    {
        $table = $this->parts[TablePart::class]->__toString();
        $columns = $this->parts[ColumnsPart::class]->__toString();
        $values = $this->parts[ValuesPart::class]->__toString();

        $sql = "INSERT INTO {$table} ({$columns}) {$values}";

        if (isset($this->parts[ReturningPart::class])) {
            $returning = $this->parts[ReturningPart::class]->__toString();
            $sql = "{$sql} {$returning}";
        }

        return $sql;
    }

    public function __toUpdate(): string
    {
        $table = $this->parts[TablePart::class]->__toString();
        $set = $this->parts[UpdateSetValuesPart::class]->__toString();

        $sql = "UPDATE {$table} {$set}";

        if (isset($this->parts[WherePartCollection::class]) && $this->parts[WherePartCollection::class]->isNotEmpty()) {
            $wheres = $this->parts[WherePartCollection::class]->__toString();
            $sql = "{$sql} {$wheres}";
        }

        if (isset($this->parts[ReturningPart::class])) {
            $returning = $this->parts[ReturningPart::class]->__toString();
            $sql = "{$sql} {$returning}";
        }

        return $sql;
    }

    public function __toDelete(): string
    {
        $table = $this->parts[TablePart::class]->__toString();

        $sql = "DELETE FROM {$table}";

        if (isset($this->parts[WherePartCollection::class]) && $this->parts[WherePartCollection::class]->isNotEmpty()) {
            $wheres = $this->parts[WherePartCollection::class]->__toString();
            $sql = "{$sql} {$wheres}";
        }

        if (isset($this->parts[ReturningPart::class])) {
            $returning = $this->parts[ReturningPart::class]->__toString();
            $sql = "{$sql} {$returning}";
        }

        return $sql;
    }

    public function __toString(): string
    {
        if (self::SELECT === $this->parts[self::TYPE]) {
            return $this->bindParameterParts($this->__toSelect());
        }

        if (self::INSERT === $this->parts[self::TYPE]) {
            return $this->bindParameterParts($this->__toInsert());
        }

        if (self::UPDATE === $this->parts[self::TYPE]) {
            return $this->bindParameterParts($this->__toUpdate());
        }

        if (self::DELETE === $this->parts[self::TYPE]) {
            return $this->bindParameterParts($this->__toDelete());
        }

        return throw new RuntimeException('Query type not supported');
    }
}

<?php

declare(strict_types=1);

namespace MySaasPackage\Support;

use Stringable;
use RuntimeException;
use MySaasPackage\Support\QueryPart\CtePart;
use MySaasPackage\Support\QueryPart\JoinPart;
use MySaasPackage\Support\QueryPart\JoinType;
use MySaasPackage\Support\QueryPart\LimitPart;
use MySaasPackage\Support\QueryPart\ParamPart;
use MySaasPackage\Support\QueryPart\TablePart;
use MySaasPackage\Support\QueryPart\WherePart;
use MySaasPackage\Support\QueryPart\WhereType;
use MySaasPackage\Support\QueryPart\ValuesPart;
use MySaasPackage\Support\QueryPart\ColumnsPart;
use MySaasPackage\Support\QueryPart\GroupByPart;
use MySaasPackage\Support\QueryPart\OrderByPart;
use MySaasPackage\Support\QueryPart\OrderByType;
use MySaasPackage\Support\QueryPart\HavingByPart;
use MySaasPackage\Support\QueryPart\ReturningPart;
use MySaasPackage\Support\QueryPart\CteCollectionPart;
use MySaasPackage\Support\QueryPart\JoinCollectionPart;
use MySaasPackage\Support\QueryPart\UpdateSetValuesPart;
use MySaasPackage\Support\QueryPart\WhereCollectionPart;
use MySaasPackage\Support\QueryPart\ParamsCollectionPart;
use MySaasPackage\Support\QueryPart\OrderByCollectionPart;

class QueryBuilder implements Stringable
{
    protected array $parts = [];

    public const TYPE = 'type';
    public const SELECT = 'select';
    public const INSERT = 'insert';
    public const UPDATE = 'update';
    public const DELETE = 'delete';

    public function __construct()
    {
        $this->parts[JoinCollectionPart::class] = new JoinCollectionPart();
        $this->parts[CteCollectionPart::class] = new CteCollectionPart();
        $this->parts[WhereCollectionPart::class] = new WhereCollectionPart();
        $this->parts[OrderByCollectionPart::class] = new OrderByCollectionPart();
        $this->parts[ParamsCollectionPart::class] = new ParamsCollectionPart();
    }

    public static function create(): self
    {
        return new self();
    }

    public function select(array $columns = []): self
    {
        $this->parts[self::TYPE] = self::SELECT;
        $this->parts[ColumnsPart::class] = new ColumnsPart($columns);

        return $this;
    }

    public function insert(string $table = null): self
    {
        $this->parts[self::TYPE] = self::INSERT;

        $this->parts[TablePart::class] = new TablePart($table);

        return $this;
    }

    public function into(string $table): self
    {
        $this->parts[self::TYPE] = self::INSERT;
        $this->parts[TablePart::class] = new TablePart($table);

        return $this;
    }

    public function values(array $values = []): self
    {
        $this->parts[self::TYPE] = self::INSERT;
        $this->parts[ColumnsPart::class] = new ColumnsPart(array_keys($values));
        $this->parts[ValuesPart::class] = new ValuesPart(array_values($values));

        return $this;
    }

    public function update(string $table, array $columns = []): self
    {
        $this->parts[self::TYPE] = self::UPDATE;
        $this->parts[TablePart::class] = new TablePart($table);
        $this->parts[ColumnsPart::class] = new ColumnsPart($columns);
        $this->parts[ColumnsPart::class] = new ColumnsPart($columns);

        return $this;
    }

    public function set(array $values = []): self
    {
        $this->parts[self::TYPE] = self::UPDATE;
        $this->parts[UpdateSetValuesPart::class] = new UpdateSetValuesPart($values);

        return $this;
    }

    public function delete(): self
    {
        $this->parts[self::TYPE] = self::DELETE;

        return $this;
    }

    public function table(string $table): self
    {
        $this->parts[TablePart::class] = new TablePart($table);

        return $this;
    }

    public function from(string $table): self
    {
        $this->parts[TablePart::class] = new TablePart($table);

        return $this;
    }

    protected function addParam(ParamPart $param): self
    {
        $this->parts[ParamsCollectionPart::class]->add($param);

        return $this;
    }

    public function bind(string $key, $value): self
    {
        $this->addParam(new ParamPart($key, $value));

        return $this;
    }

    protected function addWhere(WherePart $wherePart): self
    {
        $this->parts[WhereCollectionPart::class]->add($wherePart);

        return $this;
    }

    public function where(string $condition): self
    {
        $this->addWhere(new WherePart($condition));

        return $this;
    }

    public function andWhere(string $condition): self
    {
        $this->addWhere(new WherePart($condition, WhereType::And));

        return $this;
    }

    public function orWhere(string $condition): self
    {
        $this->addWhere(new WherePart($condition, WhereType::Or));

        return $this;
    }

    public function returning(array $columns = []): self
    {
        $this->parts[ReturningPart::class] = new ReturningPart($columns);

        return $this;
    }

    protected function addJoin(JoinPart $join): self
    {
        $this->parts[JoinCollectionPart::class]->add($join);

        return $this;
    }

    public function join(string $table, string $condition): self
    {
        $this->addJoin(new JoinPart(
            type: JoinType::Join,
            table: new TablePart($table),
            condition: $condition
        ));

        return $this;
    }

    public function leftJoin(string $table, string $condition): self
    {
        $this->addJoin(new JoinPart(
            type: JoinType::Left,
            table: new TablePart($table),
            condition: $condition
        ));

        return $this;
    }

    public function rightJoin(string $table, string $condition): self
    {
        $this->addJoin(new JoinPart(
            type: JoinType::Right,
            table: new TablePart($table),
            condition: $condition
        ));

        return $this;
    }

    public function innerJoin(string $table, string $condition): self
    {
        $this->addJoin(new JoinPart(
            type: JoinType::Inner,
            table: new TablePart($table),
            condition: $condition
        ));

        return $this;
    }

    protected function addOrderBy(OrderByPart $orderBy): self
    {
        $this->parts[OrderByCollectionPart::class]->add($orderBy);

        return $this;
    }

    public function orderBy(array $columns, string $direction = null): self
    {
        $direction ??= OrderByType::Asc;

        if (is_string($direction)) {
            $direction = OrderByType::from($direction);
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
        $this->parts[CteCollectionPart::class]->add($cte);

        return $this;
    }

    public function with(string $alias, QueryBuilder $query): self
    {
        $this->addCte(new CtePart(alias: $alias, query: $query));

        return $this;
    }

    protected function bindParamsParts(string $sql): string
    {
        $params = $this->parts[ParamsCollectionPart::class]->params;

        foreach ($params as $param) {
            $sql = preg_replace(sprintf('/:%s/', $param->name), strval($param->value), $sql);
        }

        return $sql;
    }

    public function __toSelect(): string
    {
        $table = $this->parts[TablePart::class]->__toString();
        $columns = $this->parts[ColumnsPart::class]->__toString();

        $sql = "SELECT {$columns} FROM {$table}";

        if ($this->parts[CteCollectionPart::class]->isNotEmpty()) {
            $ctes = $this->parts[CteCollectionPart::class]->__toString();
            $sql = "{$ctes} {$sql}";
        }

        if ($this->parts[JoinCollectionPart::class]->isNotEmpty()) {
            $join = $this->parts[JoinCollectionPart::class]->__toString();
            $sql = "{$sql} {$join}";
        }

        if ($this->parts[WhereCollectionPart::class]->isNotEmpty()) {
            $wheres = $this->parts[WhereCollectionPart::class]->__toString();
            $sql = "{$sql} {$wheres}";
        }

        if ($this->parts[OrderByCollectionPart::class]->isNotEmpty()) {
            $orderBy = $this->parts[OrderByCollectionPart::class]->__toString();
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

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$values})";

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

        $sql = "UPDATE {$table} SET {$set}";

        if ($this->parts[WhereCollectionPart::class]->isNotEmpty()) {
            $wheres = $this->parts[WhereCollectionPart::class]->__toString();
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

        if ($this->parts[WhereCollectionPart::class]->isNotEmpty()) {
            $wheres = $this->parts[WhereCollectionPart::class]->__toString();
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
            return $this->bindParamsParts($this->__toSelect());
        }

        if (self::INSERT === $this->parts[self::TYPE]) {
            return $this->bindParamsParts($this->__toInsert());
        }

        if (self::UPDATE === $this->parts[self::TYPE]) {
            return $this->bindParamsParts($this->__toUpdate());
        }

        if (self::DELETE === $this->parts[self::TYPE]) {
            return $this->bindParamsParts($this->__toDelete());
        }

        return throw new RuntimeException('Query type not supported');
    }
}

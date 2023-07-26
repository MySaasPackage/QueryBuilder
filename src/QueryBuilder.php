<?php

declare(strict_types=1);

namespace MySaasPackage\Support;

use RuntimeException;
use MySaasPackage\Support\QueryPart\Part;
use MySaasPackage\Support\QueryPart\LimitPart;
use MySaasPackage\Support\QueryPart\TablePart;
use MySaasPackage\Support\QueryPart\ValuesPart;
use MySaasPackage\Support\QueryPart\ColumnsPart;
use MySaasPackage\Support\QueryPart\GroupByPart;
use MySaasPackage\Support\QueryPart\HavingByPart;
use MySaasPackage\Support\QueryPart\ParameterPart;
use MySaasPackage\Support\QueryPart\ReturningPart;
use MySaasPackage\Support\QueryPart\Join\JoinTrait;
use MySaasPackage\Support\QueryPart\Where\WhereTrait;
use MySaasPackage\Support\QueryPart\UpdateSetValuesPart;
use MySaasPackage\Support\QueryPart\OrderBy\OrderByTrait;
use MySaasPackage\Support\QueryPart\ParameterPartCollection;
use MySaasPackage\Support\QueryPart\CommonTableExpression\CommonTableExpressionTrait;

class QueryBuilder implements Part
{
    use WhereTrait;
    use JoinTrait;
    use OrderByTrait;
    use CommonTableExpressionTrait;

    protected array $parts = [];

    public const TYPE = 'type';
    public const SELECT = 'select';
    public const INSERT = 'insert';
    public const UPDATE = 'update';
    public const DELETE = 'delete';

    public function __construct(protected DbDriver $drive)
    {
    }

    public static function create(DbDriver $drive): self
    {
        return new self($drive);
    }

    public static function postgres(): self
    {
        return new self(DbDriver::PostgreSQL);
    }

    public static function mysql(): self
    {
        return new self(DbDriver::MySQL);
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

    public function returning(array $columns = []): self
    {
        $this->parts[ReturningPart::class] = new ReturningPart($columns);

        return $this;
    }

    public function groupBy($columns): self
    {
        $this->parts[GroupByPart::class] = new GroupByPart($columns);

        return $this;
    }

    public function limit(int $limit, int $offset = null): self
    {
        $this->parts[LimitPart::class] = new LimitPart($this->drive, $limit, $offset);

        return $this;
    }

    public function having(string $condition): self
    {
        $this->parts[HavingByPart::class] = new HavingByPart($condition);

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

        if ($this->commonTableExpressionPartCollection) {
            $sql = "{$this->__toCommonTableExpression()} {$sql}";
        }

        if ($this->joinPartCollection) {
            $sql = "{$sql} {$this->__toJoin()}";
        }

        if ($this->wherePartsCollection) {
            $sql = "{$sql} {$this->__toWhere()}";
        }

        if ($this->orderByPartCollection) {
            $sql = "{$sql} {$this->__toOrderBy()}";
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

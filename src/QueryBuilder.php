<?php

declare(strict_types=1);

namespace MySaasPackage\Support;

use RuntimeException;
use MySaasPackage\Support\QueryPart\Part;
use MySaasPackage\Support\QueryPart\ParameterPart;
use MySaasPackage\Support\QueryPart\Set\SetModule;
use MySaasPackage\Support\QueryPart\Join\JoinModule;
use MySaasPackage\Support\QueryPart\Where\WhereTrait;
use MySaasPackage\Support\QueryPart\Limit\LimitModule;
use MySaasPackage\Support\QueryPart\Table\TableModule;
use MySaasPackage\Support\QueryPart\Columns\ColumnsModule;
use MySaasPackage\Support\QueryPart\GroupBy\GroupByModule;
use MySaasPackage\Support\QueryPart\OrderBy\OrderByModule;
use MySaasPackage\Support\QueryPart\HavingBy\HavingByModule;
use MySaasPackage\Support\QueryPart\KeyValue\KeyValueModule;
use MySaasPackage\Support\QueryPart\ParameterPartCollection;
use MySaasPackage\Support\QueryPart\Returning\ReturningModule;
use MySaasPackage\Support\QueryPart\CommonTableExpression\CommonTableExpressionModule;

class QueryBuilder implements Part
{
    use WhereTrait;
    use JoinModule;
    use OrderByModule;
    use ColumnsModule;
    use GroupByModule;
    use LimitModule;
    use HavingByModule;
    use ReturningModule;
    use SetModule;
    use TableModule;
    use KeyValueModule;
    use CommonTableExpressionModule;

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
        $this->columns($columns);

        return $this;
    }

    public function insert(string $table): self
    {
        $this->parts[self::TYPE] = self::INSERT;
        $this->table($table);

        return $this;
    }

    public function into(string $table): self
    {
        $this->table($table);

        return $this;
    }

    public function update(string $table): self
    {
        $this->parts[self::TYPE] = self::UPDATE;
        $this->table($table);

        return $this;
    }

    public function delete(): self
    {
        $this->parts[self::TYPE] = self::DELETE;

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
        $sql = "SELECT {$this->__toColumns()} FROM {$this->__toTable()}";

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

        if ($this->groupByPartCollection) {
            $sql = "{$sql} {$this->__toGroupBySql()}";
        }

        if ($this->havingByPart) {
            $sql = "{$sql} {$this->__toHavingBy()}";
        }

        if ($this->limitPart) {
            $sql = "{$sql} {$this->__toLimit()}";
        }

        if ($this->returningPart) {
            $sql = "{$sql} {$this->__toReturning()}";
        }

        return $sql;
    }

    public function __toInsert(): string
    {
        $sql = "INSERT INTO {$this->__toTable()} ({$this->__toKeys()}) ({$this->__toValues()})";

        if ($this->returningPart) {
            $sql = "{$sql} {$this->__toReturning()}";
        }

        return $sql;
    }

    public function __toUpdate(): string
    {
        $sql = "UPDATE {$this->__toTable()} {$this->__toSet()}";

        if ($this->wherePartsCollection) {
            $sql = "{$sql} {$this->__toWhere()}";
        }

        if ($this->returningPart) {
            $sql = "{$sql} {$this->__toReturning()}";
        }

        return $sql;
    }

    public function __toDelete(): string
    {
        $sql = "DELETE FROM {$this->__toTable()}";

        if ($this->returningPart) {
            $sql = "{$sql} {$this->__toReturning()}";
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

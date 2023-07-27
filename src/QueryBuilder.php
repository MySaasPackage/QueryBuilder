<?php

declare(strict_types=1);

namespace MySaasPackage\Support;

use RuntimeException;
use MySaasPackage\Support\QueryPart\Part;
use MySaasPackage\Support\QueryPart\Set\SetModule;
use MySaasPackage\Support\QueryPart\Join\JoinModule;
use MySaasPackage\Support\QueryPart\Where\WhereTrait;
use MySaasPackage\Support\QueryPart\Limit\LimitModule;
use MySaasPackage\Support\QueryPart\Table\TableModule;
use MySaasPackage\Support\QueryPart\InsertQueryBuilder;
use MySaasPackage\Support\QueryPart\SelectQueryBuilder;
use MySaasPackage\Support\QueryPart\UpdateQueryBuilder;
use MySaasPackage\Support\QueryPart\Columns\ColumnsModule;
use MySaasPackage\Support\QueryPart\GroupBy\GroupByModule;
use MySaasPackage\Support\QueryPart\OrderBy\OrderByModule;
use MySaasPackage\Support\QueryPart\HavingBy\HavingByModule;
use MySaasPackage\Support\QueryPart\KeyValue\KeyValueModule;
use MySaasPackage\Support\QueryPart\Parameter\ParameterModule;
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
    use ParameterModule;
    use CommonTableExpressionModule;

    protected array $parts = [];

    public const TYPE = 'type';
    public const SELECT = 'select';
    public const INSERT = 'insert';
    public const UPDATE = 'update';
    public const DELETE = 'delete';

    public function __construct(protected DbDriver $driver)
    {
    }

    public static function create(DbDriver $driver): self
    {
        return new self($driver);
    }

    public static function postgres(): self
    {
        return new self(DbDriver::PostgreSQL);
    }

    public static function mysql(): self
    {
        return new self(DbDriver::MySQL);
    }

    public function select(array $columns = ['*']): SelectQueryBuilder
    {
        $selectQueryBuilder = new SelectQueryBuilder($this->driver);
        $selectQueryBuilder->columns($columns);

        return $selectQueryBuilder;
    }

    public function insert(string $table): InsertQueryBuilder
    {
        $insertQueryBuilder = new InsertQueryBuilder($this->driver);
        $insertQueryBuilder->into($table);

        return $insertQueryBuilder;
    }

    public function update(string $table): UpdateQueryBuilder
    {
        $insertQueryBuilder = new UpdateQueryBuilder($this->driver);
        $insertQueryBuilder->table($table);

        return $insertQueryBuilder;
    }

    public function delete(): self
    {
        $this->parts[self::TYPE] = self::DELETE;

        return $this;
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
        if (self::DELETE === $this->parts[self::TYPE]) {
            return $this->bindParameterParts($this->__toDelete());
        }

        return throw new RuntimeException('Query type not supported');
    }
}

<?php

declare(strict_types=1);

namespace MySaasPackage;

use Stringable;
use MySaasPackage\QueryPart\DbDriver;
use MySaasPackage\QueryPart\DeleteQueryBuilder;
use MySaasPackage\QueryPart\InsertQueryBuilder;
use MySaasPackage\QueryPart\SelectQueryBuilder;
use MySaasPackage\QueryPart\UpdateQueryBuilder;

class QueryBuilder
{
    protected array $commonTableExpressionPartCollection = [];

    public function __construct(protected DbDriver $driver)
    {
    }

    public static function postgres(): self
    {
        return new self(DbDriver::PostgreSQL);
    }

    public static function mysql(): self
    {
        return new self(DbDriver::MySQL);
    }

    public function with(string $alias, Stringable|string $query): self
    {
        $this->commonTableExpressionPartCollection[] = [$alias, $query];

        return $this;
    }

    public function select(array $columns = ['*']): SelectQueryBuilder
    {
        $queryBuilder = new SelectQueryBuilder($this->driver);
        $queryBuilder->columns($columns);

        foreach ($this->commonTableExpressionPartCollection as [$alias, $expression]) {
            $queryBuilder->with($alias, $expression);
        }

        return $queryBuilder;
    }

    public function insert(string $table): InsertQueryBuilder
    {
        $queryBuilder = new InsertQueryBuilder($this->driver);
        $queryBuilder->into($table);

        foreach ($this->commonTableExpressionPartCollection as [$alias, $expression]) {
            $queryBuilder->with($alias, $expression);
        }

        return $queryBuilder;
    }

    public function update(string $table): UpdateQueryBuilder
    {
        $queryBuilder = new UpdateQueryBuilder($this->driver);
        $queryBuilder->table($table);

        foreach ($this->commonTableExpressionPartCollection as [$alias, $expression]) {
            $queryBuilder->with($alias, $expression);
        }

        return $queryBuilder;
    }

    public function delete(string $table): DeleteQueryBuilder
    {
        $queryBuilder = new DeleteQueryBuilder($this->driver);
        $queryBuilder->from($table);

        foreach ($this->commonTableExpressionPartCollection as [$alias, $expression]) {
            $queryBuilder->with($alias, $expression);
        }

        return $queryBuilder;
    }
}

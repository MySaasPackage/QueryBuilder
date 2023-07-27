<?php

declare(strict_types=1);

namespace MySaasPackage\Support;

use MySaasPackage\Support\QueryPart\DbDriver;
use MySaasPackage\Support\QueryPart\DeleteQueryBuilder;
use MySaasPackage\Support\QueryPart\InsertQueryBuilder;
use MySaasPackage\Support\QueryPart\SelectQueryBuilder;
use MySaasPackage\Support\QueryPart\UpdateQueryBuilder;
use MySaasPackage\Support\QueryPart\QueryBuilder as QueryBuilderContract;

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

    public function with(string $alias, QueryBuilderContract $query): self
    {
        $this->commonTableExpressionPartCollection[] = [
            'alias' => $alias,
            'query' => $query,
        ];

        return $this;
    }

    public function select(array $columns = ['*']): SelectQueryBuilder
    {
        $queryBuilder = new SelectQueryBuilder($this->driver);
        $queryBuilder->columns($columns);

        foreach ($this->commonTableExpressionPartCollection as $commonTableExpression) {
            $queryBuilder->with($commonTableExpression['alias'], $commonTableExpression['query']);
        }

        return $queryBuilder;
    }

    public function insert(string $table): InsertQueryBuilder
    {
        $queryBuilder = new InsertQueryBuilder($this->driver);
        $queryBuilder->into($table);

        return $queryBuilder;
    }

    public function update(string $table): UpdateQueryBuilder
    {
        $queryBuilder = new UpdateQueryBuilder($this->driver);
        $queryBuilder->table($table);

        return $queryBuilder;
    }

    public function delete(string $table): DeleteQueryBuilder
    {
        $queryBuilder = new DeleteQueryBuilder($this->driver);
        $queryBuilder->from($table);

        return $queryBuilder;
    }
}

<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

use Stringable;
use MySaasPackage\Support\QueryPart\Table\TableModule;
use MySaasPackage\Support\QueryPart\Where\WhereModule;
use MySaasPackage\Support\QueryPart\KeyValue\KeyValueModule;
use MySaasPackage\Support\QueryPart\Parameter\ParameterModule;
use MySaasPackage\Support\QueryPart\Returning\ReturningModule;

class InsertQueryBuilder implements QueryBuilder
{
    use WhereModule;
    use ReturningModule;
    use TableModule;
    use KeyValueModule;
    use ParameterModule;

    public function __construct(protected DbDriver $driver)
    {
    }

    public function into(Stringable|string $table): static
    {
        $this->table($table);

        return $this;
    }

    public function __toString(): string
    {
        $sql = "INSERT INTO {$this->__toTable()} {$this->__toKeys()} {$this->__toValues()}";

        if ($this->wherePartsCollection) {
            $sql = "{$sql} {$this->__toWhere()}";
        }

        if ($this->returningPart) {
            $sql = "{$sql} {$this->__toReturning()}";
        }

        return $this->bind($sql);
    }
}

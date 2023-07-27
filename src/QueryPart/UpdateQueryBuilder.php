<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

use MySaasPackage\Support\QueryPart\Set\SetModule;
use MySaasPackage\Support\QueryPart\Join\JoinModule;
use MySaasPackage\Support\QueryPart\Table\TableModule;
use MySaasPackage\Support\QueryPart\Where\WhereModule;
use MySaasPackage\Support\QueryPart\Parameter\ParameterModule;
use MySaasPackage\Support\QueryPart\Returning\ReturningModule;

class UpdateQueryBuilder implements QueryBuilder
{
    use WhereModule;
    use JoinModule;
    use ReturningModule;
    use SetModule;
    use TableModule;
    use ParameterModule;

    public function __construct(protected DbDriver $driver)
    {
    }

    public function __toString(): string
    {
        $sql = "UPDATE {$this->__toTable()} {$this->__toSet()}";

        if ($this->wherePartsCollection) {
            $sql = "{$sql} {$this->__toWhere()}";
        }

        if ($this->returningPart) {
            $sql = "{$sql} {$this->__toReturning()}";
        }

        return $this->bindParameterParts($sql);
    }
}

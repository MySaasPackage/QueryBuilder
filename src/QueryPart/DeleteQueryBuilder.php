<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

use MySaasPackage\Support\QueryPart\Table\TableModule;
use MySaasPackage\Support\QueryPart\Where\WhereModule;
use MySaasPackage\Support\QueryPart\Parameter\ParameterModule;
use MySaasPackage\Support\QueryPart\Returning\ReturningModule;
use MySaasPackage\Support\QueryPart\CommonTableExpression\CommonTableExpressionModule;

class DeleteQueryBuilder implements QueryBuilder
{
    use WhereModule;
    use ReturningModule;
    use TableModule;
    use ParameterModule;
    use CommonTableExpressionModule;

    public function __construct(protected DbDriver $driver)
    {
    }

    public function __toString(): string
    {
        $sql = "DELETE FROM {$this->__toTable()}";

        if ($this->commonTableExpressionPartCollection) {
            $sql = "{$this->__toCommonTableExpression()} {$sql}";
        }

        if ($this->wherePartsCollection) {
            $sql = "{$sql} {$this->__toWhere()}";
        }

        if ($this->returningPart) {
            $sql = "{$sql} {$this->__toReturning()}";
        }

        return $this->bind($sql);
    }
}

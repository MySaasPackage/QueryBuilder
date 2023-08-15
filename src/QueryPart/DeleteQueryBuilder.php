<?php

declare(strict_types=1);

namespace MySaasPackage\QueryPart;

use Stringable;
use MySaasPackage\QueryPart\Table\TableModule;
use MySaasPackage\QueryPart\Where\WhereModule;
use MySaasPackage\QueryPart\Parameter\ParameterModule;
use MySaasPackage\QueryPart\Returning\ReturningModule;
use MySaasPackage\QueryPart\CommonTableExpression\CommonTableExpressionModule;

class DeleteQueryBuilder implements Stringable
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

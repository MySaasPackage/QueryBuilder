<?php

declare(strict_types=1);

namespace MySaasPackage\QueryPart;

use Stringable;
use MySaasPackage\QueryPart\Set\SetModule;
use MySaasPackage\QueryPart\Join\JoinModule;
use MySaasPackage\QueryPart\Table\TableModule;
use MySaasPackage\QueryPart\Where\WhereModule;
use MySaasPackage\QueryPart\Parameter\ParameterModule;
use MySaasPackage\QueryPart\Returning\ReturningModule;
use MySaasPackage\QueryPart\CommonTableExpression\CommonTableExpressionModule;

class UpdateQueryBuilder implements Stringable
{
    use WhereModule;
    use JoinModule;
    use ReturningModule;
    use SetModule;
    use TableModule;
    use ParameterModule;
    use CommonTableExpressionModule;

    public function __construct(protected DbDriver $driver)
    {
    }

    public function __toString(): string
    {
        $sql = "UPDATE {$this->__toTable()} {$this->__toSet()}";

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

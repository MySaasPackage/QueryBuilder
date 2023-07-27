<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

use MySaasPackage\Support\QueryPart\Join\JoinModule;
use MySaasPackage\Support\QueryPart\Limit\LimitModule;
use MySaasPackage\Support\QueryPart\Table\TableModule;
use MySaasPackage\Support\QueryPart\Where\WhereModule;
use MySaasPackage\Support\QueryPart\Columns\ColumnsModule;
use MySaasPackage\Support\QueryPart\GroupBy\GroupByModule;
use MySaasPackage\Support\QueryPart\OrderBy\OrderByModule;
use MySaasPackage\Support\QueryPart\HavingBy\HavingByModule;
use MySaasPackage\Support\QueryPart\Parameter\ParameterModule;
use MySaasPackage\Support\QueryPart\Returning\ReturningModule;
use MySaasPackage\Support\QueryPart\CommonTableExpression\CommonTableExpressionModule;

class SelectQueryBuilder implements QueryBuilder
{
    use WhereModule;
    use JoinModule;
    use OrderByModule;
    use ColumnsModule;
    use GroupByModule;
    use LimitModule;
    use HavingByModule;
    use ReturningModule;
    use TableModule;
    use ParameterModule;
    use CommonTableExpressionModule;

    public function __construct(
        protected DbDriver $drive
    ) {
    }

    public function __toString(): string
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

        if ($this->groupByPart) {
            $sql = "{$sql} {$this->__toGroupBySql()}";
        }

        if ($this->havingByPart) {
            $sql = "{$sql} {$this->__toHavingBy()}";
        }

        if ($this->orderByPartCollection) {
            $sql = "{$sql} {$this->__toOrderBy()}";
        }

        if ($this->limitPart) {
            $sql = "{$sql} {$this->__toLimit()}";
        }

        if ($this->returningPart) {
            $sql = "{$sql} {$this->__toReturning()}";
        }

        return $this->bindParameterParts($sql);
    }
}

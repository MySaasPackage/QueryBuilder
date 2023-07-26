<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\CommonTableExpression;

use MySaasPackage\Support\QueryBuilder;
use MySaasPackage\Support\QueryPart\Part;

class CommonTableExpressionPart implements Part
{
    public function __construct(
        public readonly string $alias,
        public readonly QueryBuilder $query,
    ) {
    }

    public function __toString()
    {
        return sprintf('%s AS (%s)', $this->alias, $this->query->__toString());
    }
}

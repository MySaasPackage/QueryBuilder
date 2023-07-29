<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\CommonTableExpression;

use Stringable;
use MySaasPackage\Support\QueryPart\QueryBuilder;

class CommonTableExpressionPart implements Stringable
{
    public function __construct(
        public readonly string $alias,
        public readonly QueryBuilder|Stringable|string $query
    ) {
    }

    public function __toString()
    {
        return sprintf('%s AS (%s)', $this->alias, $this->query);
    }
}

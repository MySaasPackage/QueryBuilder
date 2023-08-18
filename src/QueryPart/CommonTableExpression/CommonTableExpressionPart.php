<?php

declare(strict_types=1);

namespace MySaasPackage\QueryPart\CommonTableExpression;

use Stringable;
use MySaasPackage\QueryPart\Stringify;

class CommonTableExpressionPart implements Stringable
{
    public function __construct(
        public readonly string $alias,
        public readonly Stringable|string $query
    ) {
    }

    public function __toString()
    {
        return sprintf('%s AS %s', $this->alias, Stringify::parse($this->query));
    }
}

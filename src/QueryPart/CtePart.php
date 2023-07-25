<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

use Stringable;
use MySaasPackage\Support\QueryBuilder;

class CtePart implements Stringable
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

<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\OrderBy;

use MySaasPackage\Support\QueryPart\QueryPart;
use MySaasPackage\Support\QueryPart\StringablePart;

class OrderByPart implements QueryPart
{
    public function __construct(
        public readonly StringablePart $column,
        public string|null $direction = null,
    ) {
    }

    public function __toString(): string
    {
        if (null === $this->direction) {
            return $this->column->__toString();
        }

        return sprintf('%s %s', $this->column->__toString(), $this->direction);
    }
}

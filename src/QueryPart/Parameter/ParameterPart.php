<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Parameter;

use MySaasPackage\Support\QueryPart\QueryPart;
use MySaasPackage\Support\QueryPart\StringablePart;

class ParameterPart implements QueryPart
{
    public function __construct(
        public readonly string|int $key,
        public readonly StringablePart $value,
    ) {
    }

    public function __toString(): string
    {
        return $this->value->__toString();
    }
}

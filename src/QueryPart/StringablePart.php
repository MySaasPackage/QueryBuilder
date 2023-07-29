<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

use Stringable;

class StringablePart implements Stringable
{
    public function __construct(
        public readonly QueryBuilder|Stringable|string $value,
    ) {
    }

    public function stringify(mixed $value): string
    {
        if ($value instanceof QueryBuilder) {
            return sprintf('(%s)', $value->__toString());
        }

        if ($value instanceof Stringable) {
            return $value->__toString();
        }

        return $value;
    }

    public function __toString(): string
    {
        return $this->stringify($this->value);
    }
}

<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

class StringablePart implements QueryPart
{
    public function __construct(
        public readonly mixed $value,
    ) {
    }

    public function stringify(mixed $value): string
    {
        if ($value instanceof QueryBuilder) {
            return sprintf('(%s)', $value->__toString());
        }

        if ($value instanceof QueryPart) {
            return $value->__toString();
        }

        return strval($value);
    }

    public function __toString(): string
    {
        return $this->stringify($this->value);
    }
}

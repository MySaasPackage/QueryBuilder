<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

class StringablePart implements QueryPart
{
    public function __construct(
        public readonly mixed $value,
    ) {
    }

    public function __toString(): string
    {
        if ($this->value instanceof QueryBuilder) {
            return sprintf('(%s)', $this->value->__toString());
        }

        if ($this->value instanceof QueryPart) {
            return $this->value->__toString();
        }

        return strval($this->value);
    }
}

<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

use MySaasPackage\Support\QueryBuilder;

class ColumnPart implements Part
{
    public function __construct(
        public readonly mixed $value,
    ) {
    }

    public function __toString(): string
    {
        if ($this->value instanceof QueryBuilder) {
            return sprintf('(%s)', strval($this->value));
        }

        if ($this->value instanceof Part) {
            return strval($this->value);
        }

        return $this->value;
    }
}

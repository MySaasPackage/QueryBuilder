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
            return sprintf('(%s)', $this->value->__toString());
        }

        if ($this->value instanceof Part) {
            return $this->value->__toString();
        }

        return strval($this->value);
    }
}

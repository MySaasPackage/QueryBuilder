<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Columns;

use MySaasPackage\Support\QueryBuilder;
use MySaasPackage\Support\QueryPart\Part;

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

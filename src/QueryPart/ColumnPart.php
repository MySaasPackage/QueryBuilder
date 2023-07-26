<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

use Stringable;
use InvalidArgumentException;

class ColumnPart implements Part
{
    public function __construct(
        protected readonly mixed $value,
    ) {
    }

    public function __toString(): string
    {
        if ($this->value instanceof Stringable) {
            return $this->value->__toString();
        }

        if (is_string($this->value)) {
            return $this->value;
        }

        if (is_array($this->value)) {
            return implode(', ', $this->value);
        }

        throw new InvalidArgumentException('Invalid column value');
    }
}

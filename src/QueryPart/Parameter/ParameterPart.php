<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Parameter;

use MySaasPackage\Support\QueryPart\QueryPart;
use MySaasPackage\Support\QueryPart\QueryBuilder;

class ParameterPart implements QueryPart
{
    public function __construct(
        public readonly string|int $key,
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

        if (is_int($value)) {
            return intval($value);
        }

        if (is_float($value)) {
            return floatval($value);
        }

        if (is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        }

        if (is_array($value)) {
            return sprintf('(%s)', implode(', ', array_map(fn (mixed $element) => $this->stringify($element), $value)));
        }

        return sprintf('\'%s\'', addslashes($value));
    }

    public function __toString(): string
    {
        return $this->stringify($this->value);
    }
}

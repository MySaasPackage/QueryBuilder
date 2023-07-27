<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Parameter;

use MySaasPackage\Support\QueryBuilder;
use MySaasPackage\Support\QueryPart\QueryPart;

class ParameterPart implements QueryPart
{
    public readonly mixed $value;

    public function __construct(
        public readonly string|int $key,
        mixed $value
    ) {
        $this->value = $this->stringify($value);
    }

    protected function stringify(mixed $value): mixed
    {
        if ($value instanceof QueryBuilder) {
            return sprintf('(%s)', $value->__toString());
        }

        if ($value instanceof QueryPart) {
            return $value->__toString();
        }

        if (is_int($value) || ctype_digit($value)) {
            return intval($value);
        }

        if (is_float($value)) {
            return floatval($value);
        }

        if (is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        }

        if (is_string($value)) {
            return sprintf('\'%s\'', $value);
        }

        if (is_array($value)) {
            return sprintf('(%s)', implode(', ', array_map(fn (mixed $element) => $this->stringify($element), $value)));
        }

        return $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

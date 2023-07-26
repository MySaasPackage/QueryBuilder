<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

class ParamPart implements Part
{
    public readonly mixed $value;

    public function __construct(
        public readonly string $name,
        mixed $value
    ) {
        $this->value = $this->sanitize($value);
    }

    public function sanitize(mixed $value): mixed
    {
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
            return sprintf('(%s)', implode(', ', array_map(fn (mixed $element) => $this->sanitize($element), $value)));
        }

        return $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

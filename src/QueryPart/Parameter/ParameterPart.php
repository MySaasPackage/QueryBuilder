<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Parameter;

use Stringable;
use MySaasPackage\Support\QueryPart\QueryBuilder;

class ParameterPart implements Stringable
{
    public function __construct(
        public readonly string|int $key,
        public readonly mixed $value,
    ) {
    }

    public function isNumeric(): bool
    {
        return is_int($this->value) || is_float($this->value);
    }

    public function getValue(): mixed
    {
        if ($this->isNumeric()) {
            return $this->value;
        }

        return $this->__toString();
    }

    public function getKey(): string
    {
        return is_int($this->key) ? '/\?/' : sprintf('/:%s/', $this->key);
    }

    public function stringify(mixed $value): string
    {
        if ($value instanceof QueryBuilder) {
            return sprintf('(%s)', $value->__toString());
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

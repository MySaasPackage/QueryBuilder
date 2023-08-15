<?php

declare(strict_types=1);

namespace MySaasPackage\QueryPart\Parameter;

use Stringable;
use MySaasPackage\QueryPart\Stringify;
use MySaasPackage\QueryPart\DeleteQueryBuilder;
use MySaasPackage\QueryPart\InsertQueryBuilder;
use MySaasPackage\QueryPart\SelectQueryBuilder;
use MySaasPackage\QueryPart\UpdateQueryBuilder;

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
        if ($value instanceof SelectQueryBuilder
            || $value instanceof InsertQueryBuilder
            || $value instanceof UpdateQueryBuilder
            || $value instanceof DeleteQueryBuilder
            || $value instanceof Stringify) {
            return Stringify::stringify($value);
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

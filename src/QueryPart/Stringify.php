<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

use Stringable;

class Stringify implements Stringable
{
    public function __construct(
        public readonly mixed $value,
    ) {
    }

    public static function create(mixed $value): self
    {
        return new self($value);
    }

    public static function stringify(mixed $value): string
    {
        if ($value instanceof QueryBuilder) {
            return sprintf('(%s)', $value->__toString());
        }

        if ($value instanceof Stringable) {
            return $value->__toString();
        }

        return strval($value);
    }

    public function __toString(): string
    {
        return self::stringify($this->value);
    }
}

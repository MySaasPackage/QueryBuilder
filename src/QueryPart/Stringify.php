<?php

declare(strict_types=1);

namespace MySaasPackage\QueryPart;

use Stringable;

class Stringify implements Stringable
{
    public function __construct(
        public readonly mixed $value,
    ) {
    }

    public static function parse(mixed $value): string
    {
        return match (true) {
            $value instanceof SelectQueryBuilder => sprintf('(%s)', $value->__toString()),
            $value instanceof InsertQueryBuilder => sprintf('(%s)', $value->__toString()),
            $value instanceof UpdateQueryBuilder => sprintf('(%s)', $value->__toString()),
            $value instanceof DeleteQueryBuilder => sprintf('(%s)', $value->__toString()),
            $value instanceof Stringable => $value->__toString(),
            default => strval($value),
        };
    }

    public function __toString(): string
    {
        return self::parse($this->value);
    }
}

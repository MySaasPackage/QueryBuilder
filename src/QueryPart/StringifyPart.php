<?php

declare(strict_types=1);

namespace MySaasPackage\QueryPart;

use Stringable;

class StringifyPart implements Stringable
{
    public function __construct(
        public readonly mixed $value,
    ) {
    }

    public static function isStringifyPart(mixed $value): bool
    {
        return $value instanceof Stringable
            || $value instanceof SelectQueryBuilder
            || $value instanceof InsertQueryBuilder
            || $value instanceof UpdateQueryBuilder
            || $value instanceof DeleteQueryBuilder
            || is_string($value) && preg_match("/^(?:\s*)\b(SELECT|UPDATE|INSERT|DELETE)\b/i", $value);
    }

    public static function parse(mixed $value): string
    {
        $pattern = "/^(?:\s*)\b(SELECT|UPDATE|INSERT|DELETE)\b/i";

        return match (true) {
            $value instanceof SelectQueryBuilder => sprintf('(%s)', $value->__toString()),
            $value instanceof InsertQueryBuilder => sprintf('(%s)', $value->__toString()),
            $value instanceof UpdateQueryBuilder => sprintf('(%s)', $value->__toString()),
            $value instanceof DeleteQueryBuilder => sprintf('(%s)', $value->__toString()),
            is_string($value) && preg_match($pattern, $value) => sprintf('(%s)', $value),
            $value instanceof Stringable => $value->__toString(),
            default => $value,
        };
    }

    public function __toString(): string
    {
        return self::parse($this->value);
    }
}

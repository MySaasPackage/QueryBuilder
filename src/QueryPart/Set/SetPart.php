<?php

declare(strict_types=1);

namespace MySaasPackage\QueryPart\Set;

use Stringable;
use MySaasPackage\QueryPart\Stringify;

class SetPart implements Stringable
{
    public function __construct(
        public readonly array $values,
    ) {
    }

    public function __toString(): string
    {
        return 'SET ' . implode(', ', array_map(
            fn (string $column, string $value) => sprintf('%s = %s', $column, Stringify::stringify($value)),
            array_keys($this->values),
            array_values($this->values)
        ));
    }
}

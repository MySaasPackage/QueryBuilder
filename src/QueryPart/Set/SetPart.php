<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Set;

use MySaasPackage\Support\QueryPart\Part;

class SetPart implements Part
{
    public function __construct(
        public readonly array $values,
    ) {
    }

    public function __toString(): string
    {
        return 'SET ' . implode(', ', array_map(
            fn (string $column, string $value) => sprintf('%s = %s', $column, $value),
            array_keys($this->values),
            array_values($this->values)
        ));
    }
}
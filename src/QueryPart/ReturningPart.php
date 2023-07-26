<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

class ReturningPart implements Part
{
    public function __construct(
        public readonly array $columns,
    ) {
    }

    public function __toString(): string
    {
        if (0 === count($this->columns)) {
            return '';
        }

        return 'RETURNING ' . implode(', ', array_map(fn ($column) => strtolower(trim($column)), $this->columns));
    }
}

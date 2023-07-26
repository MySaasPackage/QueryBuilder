<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

class GroupByPart implements Part
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

        return 'GROUP BY ' . implode(', ', array_map(fn ($column) => strtolower(trim($column)), $this->columns));
    }
}

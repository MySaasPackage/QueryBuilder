<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\KeyValue;

use MySaasPackage\Support\QueryPart\QueryPart;

class ValuesPart implements QueryPart
{
    public function __construct(
        public readonly array $columns,
    ) {
    }

    public function __toString(): string
    {
        return 'VALUES (' . implode(', ', array_map(fn ($column) => strtolower(trim($column)), $this->columns)) . ')';
    }
}
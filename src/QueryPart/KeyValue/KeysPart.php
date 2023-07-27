<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\KeyValue;

use MySaasPackage\Support\QueryPart\QueryPart;

class KeysPart implements QueryPart
{
    public function __construct(
        public readonly array $columns,
    ) {
    }

    public function __toString(): string
    {
        return '(' . implode(', ', array_map(fn ($column) => strval($column), $this->columns)) . ')';
    }
}

<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\KeyValue;

use Stringable;

class ValuesPart implements Stringable
{
    public function __construct(
        public readonly array $columns,
    ) {
    }

    public function __toString(): string
    {
        return 'VALUES (' . implode(', ', array_map(fn ($column) => strval($column), $this->columns)) . ')';
    }
}

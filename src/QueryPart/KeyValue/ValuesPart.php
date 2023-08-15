<?php

declare(strict_types=1);

namespace MySaasPackage\QueryPart\KeyValue;

use Stringable;

class ValuesPart implements Stringable
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

        return 'VALUES (' . implode(', ', $this->columns) . ')';
    }
}

<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Columns;

use MySaasPackage\Support\QueryPart\Part;

class ColumnsPart implements Part
{
    public function __construct(
        public readonly array $columns,
    ) {
    }

    public function __toString(): string
    {
        if (0 === count($this->columns)) {
            return '*';
        }

        $columns = array_map(fn ($column) => strval($column), $this->columns);

        return implode(', ', $columns);
    }
}

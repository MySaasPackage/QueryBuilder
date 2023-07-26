<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

class ColumnsPart implements Part
{
    public function __construct(
        public readonly array $columns,
    ) {
    }

    public function fromRawArray(array $columns): self
    {
        return new self(array_map(fn ($column) => new ColumnPart($column), $columns));
    }

    public function __toString(): string
    {
        if (0 === count($this->columns)) {
            return '*';
        }

        $columns = array_map(fn ($column) => $column, $this->columns);

        return implode(', ', $columns);
    }
}

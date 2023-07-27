<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Columns;

use MySaasPackage\Support\QueryPart\Part;
use MySaasPackage\Support\QueryPart\StringablePart;

class ColumnsPart implements Part
{
    public array $columns = [];

    public function add(StringablePart $column): self
    {
        $this->columns[] = $column;

        return $this;
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

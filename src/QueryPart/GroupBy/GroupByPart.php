<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\GroupBy;

use MySaasPackage\Support\QueryPart\Part;
use MySaasPackage\Support\QueryPart\HavingBy\StringablePart;

class GroupByPartCollection implements Part
{
    public readonly array $columns;

    public function add(StringablePart $column): self
    {
        $this->columns[] = $column;

        return $this;
    }

    public function __toString(): string
    {
        if (0 === count($this->columns)) {
            return '';
        }

        return 'GROUP BY ' . implode(', ', array_map(fn ($column) => strtolower(trim($column)), $this->columns));
    }
}

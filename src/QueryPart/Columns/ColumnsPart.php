<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Columns;

use MySaasPackage\Support\QueryPart\QueryPart;
use MySaasPackage\Support\QueryPart\StringablePart;

class ColumnsPart implements QueryPart
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

        return implode(', ', $this->columns);
    }
}

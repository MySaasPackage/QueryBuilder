<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Columns;

use Stringable;

class ColumnsPart implements Stringable
{
    public array $columns = [];

    public function add(Stringable|string $column): self
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

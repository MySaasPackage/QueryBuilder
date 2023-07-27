<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\GroupBy;

use Stringable;

class GroupByPart implements Stringable
{
    public array $columns;

    public function add(Stringable|string $column): static
    {
        $this->columns[] = $column;

        return $this;
    }

    public function __toString(): string
    {
        if (0 === count($this->columns)) {
            return '';
        }

        return 'GROUP BY ' . implode(', ', $this->columns);
    }
}

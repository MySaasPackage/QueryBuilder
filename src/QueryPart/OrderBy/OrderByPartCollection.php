<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\OrderBy;

use MySaasPackage\Support\QueryPart\QueryPart;

class OrderByPartCollection implements QueryPart
{
    protected array $orderByParts = [];

    public function add(OrderByPart $part): void
    {
        $this->orderByParts[] = $part;
    }

    public function isNotEmpty(): bool
    {
        return 0 !== count($this->orderByParts);
    }

    public function __toString(): string
    {
        if (0 === count($this->orderByParts)) {
            return '';
        }

        return 'ORDER BY ' . implode(' ', $this->orderByParts);
    }
}

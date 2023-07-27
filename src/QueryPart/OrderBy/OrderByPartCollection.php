<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\OrderBy;

use Stringable;

class OrderByPartCollection implements Stringable
{
    protected array $orderByParts = [];

    public function add(OrderByPart $part): void
    {
        $this->orderByParts[] = $part;
    }

    public function __toString(): string
    {
        if (0 === count($this->orderByParts)) {
            return '';
        }

        return 'ORDER BY ' . implode(' ', $this->orderByParts);
    }
}

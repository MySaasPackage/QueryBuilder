<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

use Stringable;

class OrderByCollectionPart implements Stringable
{
    public function __construct(
        protected array $parts = []
    ) {
    }

    public function add(OrderByPart $part): void
    {
        $this->parts[] = $part;
    }

    public function __toString(): string
    {
        if (0 === count($this->parts)) {
            return '';
        }

        return 'ORDER BY ' . implode(' ', $this->parts);
    }
}

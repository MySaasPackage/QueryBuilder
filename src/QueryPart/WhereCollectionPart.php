<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

use Stringable;

class WhereCollectionPart implements Stringable
{
    public function __construct(
        protected array $parts = []
    ) {
    }

    public function add(WherePart $part): void
    {
        $this->parts[] = $part;
    }

    public function __toString(): string
    {
        if (0 === count($this->parts)) {
            return '';
        }

        return 'WHERE ' . implode(' ', $this->parts);
    }
}

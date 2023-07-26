<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

class WhereCollectionPart implements Part
{
    public function __construct(
        protected array $parts = []
    ) {
    }

    public function add(WherePart $part): void
    {
        $this->parts[] = $part;
    }

    public function isNotEmpty(): bool
    {
        return 0 !== count($this->parts);
    }

    public function __toString(): string
    {
        if (0 === count($this->parts)) {
            return '';
        }

        return 'WHERE ' . implode(' ', $this->parts);
    }
}

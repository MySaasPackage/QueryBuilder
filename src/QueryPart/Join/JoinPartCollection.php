<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Join;

use MySaasPackage\Support\QueryPart\QueryPart;

class JoinPartCollection implements QueryPart
{
    public function __construct(
        protected array $parts = []
    ) {
    }

    public function add(JoinPart $part): void
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

        return implode(' ', $this->parts);
    }
}

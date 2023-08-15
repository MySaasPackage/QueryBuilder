<?php

declare(strict_types=1);

namespace MySaasPackage\QueryPart\Where;

use Stringable;

class WherePartCollection implements Stringable
{
    protected array $parts = [];

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

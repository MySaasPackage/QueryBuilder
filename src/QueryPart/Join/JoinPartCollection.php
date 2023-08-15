<?php

declare(strict_types=1);

namespace MySaasPackage\QueryPart\Join;

use Stringable;

class JoinPartCollection implements Stringable
{
    protected array $parts = [];

    public function add(JoinPart $part): void
    {
        $this->parts[] = $part;
    }

    public function __toString(): string
    {
        return implode(' ', $this->parts);
    }
}

<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\HavingBy;

use Stringable;
use MySaasPackage\Support\QueryPart\StringablePart;

class HavingByPart implements Stringable
{
    public function __construct(
        public readonly StringablePart|string $condition
    ) {
    }

    public function __toString(): string
    {
        return 'HAVING ' . strval($this->condition);
    }
}

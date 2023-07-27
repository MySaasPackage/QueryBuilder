<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\HavingBy;

use Stringable;

class HavingByPart implements Stringable
{
    public function __construct(
        public readonly Stringable|string $condition
    ) {
    }

    public function __toString(): string
    {
        return 'HAVING ' . strval($this->condition);
    }
}

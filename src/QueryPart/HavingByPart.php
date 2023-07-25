<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

use Stringable;

class HavingByPart implements Stringable
{
    public function __construct(
        public readonly string $condition
    ) {
    }

    public function __toString(): string
    {
        return 'HAVING ' . $this->condition;
    }
}

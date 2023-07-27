<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\HavingBy;

use MySaasPackage\Support\QueryPart\Part;
use MySaasPackage\Support\QueryPart\StringablePart;

class HavingByPart implements Part
{
    public function __construct(
        public readonly StringablePart $condition
    ) {
    }

    public function __toString(): string
    {
        return 'HAVING ' . $this->condition->__toString();
    }
}

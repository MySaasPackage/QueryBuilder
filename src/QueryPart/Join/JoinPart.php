<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Join;

use MySaasPackage\Support\QueryPart\Part;
use MySaasPackage\Support\QueryPart\TablePart;

class JoinPart implements Part
{
    public function __construct(
        public readonly Join $type,
        public readonly TablePart $table,
        public readonly string $condition
    ) {
    }

    public function __toString(): string
    {
        return sprintf('%s %s ON %s', $this->type->value, $this->table->__toString(), $this->condition);
    }
}

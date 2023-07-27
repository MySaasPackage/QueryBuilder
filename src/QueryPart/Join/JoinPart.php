<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Join;

use MySaasPackage\Support\QueryPart\QueryPart;
use MySaasPackage\Support\QueryPart\Table\TablePart;

class JoinPart implements QueryPart
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

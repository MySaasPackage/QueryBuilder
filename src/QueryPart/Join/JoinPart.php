<?php

declare(strict_types=1);

namespace MySaasPackage\QueryPart\Join;

use Stringable;
use MySaasPackage\QueryPart\Table\TablePart;

class JoinPart implements Stringable
{
    public function __construct(
        public readonly Join $type,
        public readonly TablePart $table,
        public readonly Stringable|string $condition
    ) {
    }

    public function __toString(): string
    {
        return sprintf('%s %s ON %s', $this->type->value, $this->table, $this->condition);
    }
}

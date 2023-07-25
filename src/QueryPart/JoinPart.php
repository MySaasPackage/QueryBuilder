<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

use Stringable;

class JoinPart implements Stringable
{
    public function __construct(
        public readonly JoinType $type,
        public readonly TablePart $table,
        public readonly string $condition
    ) {
    }

    public function __toString(): string
    {
        return sprintf('%s %s ON %s', $this->type->value, $this->table->__toString(), $this->condition);
    }
}

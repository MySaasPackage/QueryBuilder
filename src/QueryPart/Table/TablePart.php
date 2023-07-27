<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Table;

use MySaasPackage\Support\QueryPart\QueryPart;
use MySaasPackage\Support\QueryPart\StringablePart;

class TablePart implements QueryPart
{
    public function __construct(
        public readonly StringablePart $table,
        public readonly string|null $alias = null
    ) {
    }

    public function __toString()
    {
        if ($this->alias) {
            return sprintf('%s AS %s', $this->table, $this->alias);
        }

        return sprintf('%s', $this->table);
    }
}

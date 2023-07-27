<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Table;

use Stringable;

class TablePart implements Stringable
{
    public function __construct(
        public readonly Stringable|string $table,
        public readonly string|null $alias = null
    ) {
    }

    public function __toString()
    {
        if ($this->alias) {
            return sprintf('%s AS %s', strval($this->table), $this->alias);
        }

        return sprintf('%s', strval($this->table));
    }
}

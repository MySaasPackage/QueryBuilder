<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Table;

use InvalidArgumentException;
use MySaasPackage\Support\QueryPart\Part;

class TablePart implements Part
{
    public const TABLE_PATTERN = '/^[a-z_.]+$/';

    public function __construct(
        public readonly string $table,
        public readonly ?string $alias = null
    ) {
        if (!preg_match(self::TABLE_PATTERN, $this->table)) {
            throw new InvalidArgumentException(sprintf('Invalid table name: %s', $this->table));
        }
    }

    public function __toString()
    {
        if ($this->alias) {
            return sprintf('%s AS %s', $this->table, $this->alias);
        }

        return sprintf('%s', $this->table);
    }
}

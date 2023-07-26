<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

use InvalidArgumentException;

class TablePart implements Part
{
    public const TABLE_PATTERN = '/^[a-z_.]+$/';

    public function __construct(
        public readonly string $table
    ) {
        if (!preg_match(self::TABLE_PATTERN, $this->table)) {
            throw new InvalidArgumentException(sprintf('Invalid table name: %s', $this->table));
        }
    }

    public function __toString()
    {
        return sprintf('%s', $this->table);
    }
}

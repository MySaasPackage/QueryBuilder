<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

class OrderByPart implements Part
{
    public function __construct(
        public readonly ColumnPart $column,
        public readonly string $direction,
    ) {
    }

    public function __toString(): string
    {
        return sprintf(
            '%s %s',
            $this->column,
            $this->direction,
        );
    }
}

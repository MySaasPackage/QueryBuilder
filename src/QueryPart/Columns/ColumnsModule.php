<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Columns;

use MySaasPackage\Support\QueryPart\StringablePart;

trait ColumnsModule
{
    protected ColumnsPart|null $columns = null;

    public function columns(array $columns = ['*']): self
    {
        $this->columns = new ColumnsPart(array_map(fn ($column) => new StringablePart($column), $columns));

        return $this;
    }

    public function __toColumns(): string
    {
        return $this->columns?->__toString();
    }
}

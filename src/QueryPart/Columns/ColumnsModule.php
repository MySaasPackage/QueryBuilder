<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Columns;

trait ColumnsModule
{
    protected ColumnsPart|null $columns = null;

    public function columns(array $columns = ['*']): self
    {
        $this->columns = new ColumnsPart(array_map(fn ($column) => new ColumnPart($column), $columns));

        return $this;
    }

    public function __toColumns(): string
    {
        return $this->columns?->__toString();
    }
}

<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Columns;

use MySaasPackage\Support\QueryPart\StringablePart;

trait ColumnsModule
{
    protected ColumnsPart|null $columns = null;

    protected function addColumn(StringablePart $column): void
    {
        $this->columns ??= new ColumnsPart();
        $this->columns->add($column);
    }

    public function columns(array $columns = ['*']): self
    {
        foreach ($columns as $column) {
            $this->addColumn(new StringablePart($column));
        }

        return $this;
    }

    public function __toColumns(): string
    {
        return $this->columns?->__toString();
    }
}

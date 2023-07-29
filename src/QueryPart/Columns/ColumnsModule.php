<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Columns;

use Stringable;

trait ColumnsModule
{
    protected ColumnsPart|null $columns = null;

    protected function addColumnToCollection(Stringable|string $column): void
    {
        $this->columns ??= new ColumnsPart();

        $this->columns->add($column);
    }

    public function columns(array $columns = []): self
    {
        foreach ($columns as $column) {
            $this->addColumnToCollection($column);
        }

        return $this;
    }

    public function addCollumn(Stringable|string $column): self
    {
        $this->addColumnToCollection($column);

        return $this;
    }

    public function __toColumns(): string
    {
        return $this->columns?->__toString() ?? '';
    }
}

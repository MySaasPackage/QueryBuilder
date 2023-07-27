<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Table;

trait TableModule
{
    protected TablePart|null $tablePart = null;

    public function table(string $table, string $alias = null): self
    {
        $this->tablePart = new TablePart($table, $alias);

        return $this;
    }

    public function from(string $table, string $alias = null): self
    {
        $this->tablePart = new TablePart($table, $alias);

        return $this;
    }

    public function __toTable(): string
    {
        return $this->tablePart?->__toString() ?? '';
    }
}

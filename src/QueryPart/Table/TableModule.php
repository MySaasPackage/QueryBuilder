<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Table;

use MySaasPackage\Support\QueryPart\StringablePart;

trait TableModule
{
    protected TablePart|null $tablePart = null;

    public function table(StringablePart|string $table, string $alias = null): static
    {
        $this->tablePart = new TablePart($table, $alias);

        return $this;
    }

    public function from(StringablePart|string $table, string $alias = null): static
    {
        $this->tablePart = new TablePart($table, $alias);

        return $this;
    }

    public function __toTable(): string
    {
        return $this->tablePart?->__toString() ?? '';
    }
}

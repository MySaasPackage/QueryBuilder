<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Table;

use Stringable;

trait TableModule
{
    protected TablePart|null $tablePart = null;

    public function table(Stringable|string $table, string $alias = null): static
    {
        $this->tablePart = new TablePart($table, $alias);

        return $this;
    }

    public function from(Stringable|string $table, string $alias = null): static
    {
        $this->tablePart = new TablePart($table, $alias);

        return $this;
    }

    public function __toTable(): string
    {
        return $this->tablePart?->__toString() ?? '';
    }
}

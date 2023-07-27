<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\GroupBy;

use Stringable;

trait GroupByModule
{
    protected GroupByPart|null $groupByPart = null;

    protected function addGroupByPart(Stringable|string $groupBy): static
    {
        $this->groupByPart ??= new GroupByPart();
        $this->groupByPart->add($groupBy);

        return $this;
    }

    public function groupBy(Stringable|string $column): static
    {
        $this->addGroupByPart($column);

        return $this;
    }

    public function addGroupBy(Stringable|string $column): static
    {
        $this->addGroupByPart($column);

        return $this;
    }

    protected function __toGroupBySql(): string
    {
        return $this->groupByPart?->__toString() ?? '';
    }
}

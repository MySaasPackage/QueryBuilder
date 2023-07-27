<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\GroupBy;

use MySaasPackage\Support\QueryPart\StringablePart;

trait GroupByModule
{
    protected GroupByPart|null $groupByPart = null;

    protected function addGroupByPart(StringablePart $groupBy): self
    {
        $this->groupByPart ??= new GroupByPart();
        $this->groupByPart->add($groupBy);

        return $this;
    }

    public function groupBy(string $column): self
    {
        $this->addGroupByPart(new StringablePart($column));

        return $this;
    }

    public function addGroupBy(string $column): self
    {
        $this->addGroupByPart(new StringablePart($column));

        return $this;
    }

    protected function __toGroupBySql(): string
    {
        return $this->groupByPart?->__toString();
    }
}

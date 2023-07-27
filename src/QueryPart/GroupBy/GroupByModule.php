<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\GroupBy;

use MySaasPackage\Support\QueryPart\StringablePart;

trait GroupByModule
{
    protected GroupByPartCollection|null $groupByPartCollection = null;

    protected function addGroupByPartToCollection(StringablePart $groupBy): self
    {
        $this->groupByPartCollection ??= new GroupByPartCollection();
        $this->groupByPartCollection->add($groupBy);

        return $this;
    }

    public function groupBy(string $column): self
    {
        $this->addGroupByPartToCollection(new StringablePart($column));

        return $this;
    }

    public function addGroupBy(string $column): self
    {
        $this->addGroupByPartToCollection(new StringablePart($column));

        return $this;
    }

    protected function __toGroupBySql(): string
    {
        return $this->groupByPartCollection?->__toString();
    }
}

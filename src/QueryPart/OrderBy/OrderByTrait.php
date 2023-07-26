<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\OrderBy;

use MySaasPackage\Support\QueryPart\ColumnPart;

trait OrderByTrait
{
    protected OrderByPartCollection|null $orderByPartCollection = null;

    protected function addOrderBy(OrderByPart $orderBy): self
    {
        $this->orderByPartCollection ??= new OrderByPartCollection();
        $this->orderByPartCollection->add($orderBy);

        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->addOrderBy(new OrderByPart(new ColumnPart($column), $direction));

        return $this;
    }

    public function __toOrderBy(): string
    {
        return $this->orderByPartCollection?->__toString();
    }
}

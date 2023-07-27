<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\OrderBy;

use MySaasPackage\Support\QueryPart\StringablePart;

trait OrderByModule
{
    protected OrderByPartCollection|null $orderByPartCollection = null;

    protected function addOrderByPartToCollection(OrderByPart $orderBy): self
    {
        $this->orderByPartCollection ??= new OrderByPartCollection();
        $this->orderByPartCollection->add($orderBy);

        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->addOrderByPartToCollection(new OrderByPart(new StringablePart($column), $direction));

        return $this;
    }

    public function addOrderBy(string $column, string $direction = 'ASC'): self
    {
        $this->addOrderByPartToCollection(new OrderByPart(new StringablePart($column), $direction));

        return $this;
    }

    protected function __toOrderBy(): string
    {
        return $this->orderByPartCollection?->__toString();
    }
}

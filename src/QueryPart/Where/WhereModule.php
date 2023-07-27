<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Where;

use Stringable;

trait WhereModule
{
    protected WherePartCollection|null $wherePartsCollection = null;

    protected function addWhere(WherePart $wherePart): static
    {
        $this->wherePartsCollection ??= new WherePartCollection();
        $this->wherePartsCollection->add($wherePart);

        return $this;
    }

    public function where(Stringable|string $condition): static
    {
        $this->addWhere(new WherePart($condition));

        return $this;
    }

    public function andWhere(Stringable|string $condition): static
    {
        $this->addWhere(new WherePart($condition, Where::AND));

        return $this;
    }

    public function orWhere(Stringable|string $condition): static
    {
        $this->addWhere(new WherePart($condition, Where::OR));

        return $this;
    }

    public function __toWhere(): string
    {
        return $this->wherePartsCollection?->__toString() ?? '';
    }
}

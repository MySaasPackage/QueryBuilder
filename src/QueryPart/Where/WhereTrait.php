<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Where;

trait WhereTrait
{
    protected WherePartCollection|null $wherePartsCollection = null;

    protected function addWhere(WherePart $wherePart): self
    {
        $this->wherePartsCollection ??= new WherePartCollection();
        $this->wherePartsCollection->add($wherePart);

        return $this;
    }

    public function where(string $condition): self
    {
        $this->addWhere(new WherePart($condition));

        return $this;
    }

    public function andWhere(string $condition): self
    {
        $this->addWhere(new WherePart($condition, Where::AND));

        return $this;
    }

    public function orWhere(string $condition): self
    {
        $this->addWhere(new WherePart($condition, Where::OR));

        return $this;
    }

    public function __toWhere(): string
    {
        return $this->wherePartsCollection->__toString();
    }
}

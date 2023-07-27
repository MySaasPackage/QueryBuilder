<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Limit;

trait LimitModule
{
    protected LimitPart|null $limitPart = null;

    public function limit(int $limit, int $offset = null): static
    {
        $this->limitPart = new LimitPart($this->drive, $limit, $offset);

        return $this;
    }

    public function __toLimit(): string
    {
        return $this->limitPart?->__toString();
    }
}

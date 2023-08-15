<?php

declare(strict_types=1);

namespace MySaasPackage\QueryPart\HavingBy;

use Stringable;

trait HavingByModule
{
    protected HavingByPart|null $havingByPart = null;

    public function having(Stringable|string $condition): static
    {
        $this->havingByPart = new HavingByPart($condition);

        return $this;
    }

    public function __toHavingBy(): string
    {
        return $this->havingByPart?->__toString() ?? '';
    }
}

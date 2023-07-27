<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\HavingBy;

use MySaasPackage\Support\QueryPart\StringablePart;

trait HavingByModule
{
    protected HavingByPart|null $havingByPart = null;

    public function having(StringablePart|string $condition): static
    {
        $this->havingByPart = new HavingByPart($condition);

        return $this;
    }

    public function __toHavingBy(): string
    {
        return $this->havingByPart?->__toString() ?? '';
    }
}

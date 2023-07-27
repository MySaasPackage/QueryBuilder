<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\HavingBy;

use MySaasPackage\Support\QueryPart\StringablePart;

trait HavingByModule
{
    protected HavingByPart|null $havingByPart = null;

    public function having(string $condition): self
    {
        $this->havingByPart = new HavingByPart(new StringablePart($condition));

        return $this;
    }

    public function __toHavingBy(): string
    {
        return $this->havingByPart?->__toString();
    }
}

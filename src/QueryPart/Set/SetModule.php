<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Set;

trait SetModule
{
    protected SetPart|null $setPart = null;

    public function set(array $values = []): static
    {
        $this->setPart = new SetPart($values);

        return $this;
    }

    public function __toSet(): string
    {
        return $this->setPart?->__toString() ?? '';
    }
}

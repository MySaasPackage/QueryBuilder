<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Returning;

trait ReturningModule
{
    protected ReturningPart|null $returningPart = null;

    public function returning(array $columns = []): static
    {
        $this->returningPart = new ReturningPart($columns);

        return $this;
    }

    public function __toReturning(): string
    {
        return $this->returningPart?->__toString() ?? '';
    }
}

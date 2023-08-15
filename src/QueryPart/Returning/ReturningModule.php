<?php

declare(strict_types=1);

namespace MySaasPackage\QueryPart\Returning;

use Stringable;

trait ReturningModule
{
    protected ReturningPart|null $returningPart = null;

    protected function addReturningToCollection(Stringable|string $column): void
    {
        $this->returningPart ??= new ReturningPart();
        $this->returningPart->add($column);
    }

    public function returning(array $columns = []): static
    {
        foreach ($columns as $column) {
            $this->addReturningToCollection($column);
        }

        return $this;
    }

    public function addReturning(Stringable|string $column): self
    {
        $this->addReturningToCollection($column);

        return $this;
    }

    public function __toReturning(): string
    {
        return $this->returningPart?->__toString() ?? '';
    }
}

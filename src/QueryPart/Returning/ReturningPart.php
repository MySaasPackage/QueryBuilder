<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Returning;

use Stringable;

class ReturningPart implements Stringable
{
    public array $returning = [];

    public function add(Stringable|string $column): self
    {
        $this->returning[] = $column;

        return $this;
    }

    public function __toString(): string
    {
        if (0 === count($this->returning)) {
            return '*';
        }

        return 'RETURNING ' . implode(', ', $this->returning);
    }
}

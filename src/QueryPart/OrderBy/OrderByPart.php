<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\OrderBy;

use Stringable;

class OrderByPart implements Stringable
{
    public function __construct(
        public readonly Stringable|string $column,
        public string|null $direction = null,
    ) {
    }

    public function __toString(): string
    {
        if (null === $this->direction) {
            return strval($this->column);
        }

        return sprintf('%s %s', strval($this->column), $this->direction);
    }
}

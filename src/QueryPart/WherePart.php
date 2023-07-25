<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

use Stringable;

class WherePart implements Stringable
{
    public function __construct(
        public readonly string $condition,
        public readonly WhereType|null $type = null
    ) {
    }

    public function __toString(): string
    {
        if (null === $this->type) {
            return $this->condition;
        }

        return sprintf('%s %s', $this->type->value, $this->condition);
    }
}

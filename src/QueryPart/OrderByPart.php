<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

use Stringable;

class OrderByPart implements Stringable
{
    public function __construct(
        public readonly array $columns,
        public readonly OrderByType|null $orderType = null,
    ) {
    }

    public function __toString(): string
    {
        if (0 === count($this->columns)) {
            return '';
        }

        $order = implode(', ', array_map(fn ($column) => strtolower(trim($column)), $this->columns));

        if (null !== $this->orderType) {
            $order = "{$order} {$this->orderType->value}";
        }

        return $order;
    }
}

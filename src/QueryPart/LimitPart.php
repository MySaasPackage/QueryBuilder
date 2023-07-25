<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

use Stringable;
use InvalidArgumentException;

class LimitPart implements Stringable
{
    public function __construct(
        public readonly int $limit
    ) {
        if ($limit < 1) {
            throw new InvalidArgumentException('Limit must be greater than 0');
        }
    }

    public function __toString()
    {
        return "LIMIT {$this->limit}";
    }
}

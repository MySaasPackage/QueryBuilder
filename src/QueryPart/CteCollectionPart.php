<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

use Stringable;

class CteCollectionPart implements Stringable
{
    public function __construct(
        public array $ctes = []
    ) {
    }

    public function add(CtePart $cte): self
    {
        $this->ctes[] = $cte;

        return $this;
    }

    public function __toString()
    {
        if (0 === count($this->ctes)) {
            return '';
        }

        return 'WITH ' . implode(', ', $this->ctes);
    }
}

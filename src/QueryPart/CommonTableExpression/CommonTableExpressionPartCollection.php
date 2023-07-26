<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\CommonTableExpression;

use MySaasPackage\Support\QueryPart\Part;

class CommonTableExpressionPartCollection implements Part
{
    public function __construct(
        public array $ctes = []
    ) {
    }

    public function add(CommonTableExpressionPart $cte): self
    {
        $this->ctes[] = $cte;

        return $this;
    }

    public function isNotEmpty(): bool
    {
        return 0 !== count($this->ctes);
    }

    public function __toString()
    {
        if (0 === count($this->ctes)) {
            return '';
        }

        return 'WITH ' . implode(', ', $this->ctes);
    }
}

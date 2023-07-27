<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\CommonTableExpression;

use MySaasPackage\Support\QueryPart\QueryPart;

class CommonTableExpressionPartCollection implements QueryPart
{
    protected array $commonTableExpressions = [];

    public function add(CommonTableExpressionPart $commonTableExpression): static
    {
        $this->commonTableExpressions[] = $commonTableExpression;

        return $this;
    }

    public function isNotEmpty(): bool
    {
        return 0 !== count($this->commonTableExpressions);
    }

    public function __toString()
    {
        if (0 === count($this->commonTableExpressions)) {
            return '';
        }

        return 'WITH ' . implode(', ', $this->commonTableExpressions);
    }
}

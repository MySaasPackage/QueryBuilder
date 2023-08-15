<?php

declare(strict_types=1);

namespace MySaasPackage\QueryPart\CommonTableExpression;

use Stringable;

class CommonTableExpressionPartCollection implements Stringable
{
    protected array $commonTableExpressions = [];

    public function add(CommonTableExpressionPart $commonTableExpression): static
    {
        $this->commonTableExpressions[] = $commonTableExpression;

        return $this;
    }

    public function __toString()
    {
        if (0 === count($this->commonTableExpressions)) {
            return '';
        }

        return 'WITH ' . implode(', ', $this->commonTableExpressions);
    }
}

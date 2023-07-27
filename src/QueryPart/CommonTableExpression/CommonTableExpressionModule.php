<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\CommonTableExpression;

use MySaasPackage\Support\QueryPart\QueryBuilder;

trait CommonTableExpressionModule
{
    protected CommonTableExpressionPartCollection|null $commonTableExpressionPartCollection = null;

    protected function addCommonTableExpressionToCollection(CommonTableExpressionPart $commonTableExpression): static
    {
        $this->commonTableExpressionPartCollection ??= new CommonTableExpressionPartCollection();
        $this->commonTableExpressionPartCollection->add($commonTableExpression);

        return $this;
    }

    public function with(string $alias, QueryBuilder $query): static
    {
        $this->addCommonTableExpressionToCollection(new CommonTableExpressionPart($alias, $query));

        return $this;
    }

    protected function __toCommonTableExpression(): string
    {
        return $this->commonTableExpressionPartCollection->__toString();
    }
}

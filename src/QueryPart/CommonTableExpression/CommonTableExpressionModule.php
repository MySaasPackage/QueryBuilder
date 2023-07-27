<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\CommonTableExpression;

use MySaasPackage\Support\QueryBuilder;

trait CommonTableExpressionModule
{
    protected CommonTableExpressionPartCollection|null $commonTableExpressionPartCollection = null;

    protected function addCommonTableExpressionToCollection(CommonTableExpressionPart $cte): self
    {
        $this->commonTableExpressionPartCollection ??= new CommonTableExpressionPartCollection();
        $this->commonTableExpressionPartCollection->add($cte);

        return $this;
    }

    public function with(string $alias, QueryBuilder $query): self
    {
        $this->addCommonTableExpressionToCollection(new CommonTableExpressionPart(alias: $alias, query: $query));

        return $this;
    }

    protected function __toCommonTableExpression(): string
    {
        return $this->joinPartCollection->__toString();
    }
}

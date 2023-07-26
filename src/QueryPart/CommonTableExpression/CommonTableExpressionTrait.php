<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\CommonTableExpression;

use MySaasPackage\Support\QueryBuilder;

trait CommonTableExpressionTrait
{
    protected CommonTableExpressionPartCollection|null $commonTableExpressionPartCollection = null;

    protected function addCte(CommonTableExpressionPart $cte): self
    {
        $this->commonTableExpressionPartCollection ??= new CommonTableExpressionPartCollection();
        $this->commonTableExpressionPartCollection->add($cte);

        return $this;
    }

    public function with(string $alias, QueryBuilder $query): self
    {
        $this->addCte(new CommonTableExpressionPart(alias: $alias, query: $query));

        return $this;
    }

    public function __toCommonTableExpression(): string
    {
        return $this->joinPartCollection->__toString();
    }
}

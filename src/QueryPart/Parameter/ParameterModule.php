<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Parameter;

use Stringable;

trait ParameterModule
{
    protected ParameterPartCollection|null $parameterPartCollection = null;

    protected function addParam(ParameterPart $param): self
    {
        $this->parameterPartCollection ??= new ParameterPartCollection();
        $this->parameterPartCollection->add($param);

        return $this;
    }

    public function setParameter(string|int $key, mixed $value): static
    {
        $this->addParam(new ParameterPart($key, $value));

        return $this;
    }

    protected function bind(Stringable|string $query): Stringable|string
    {
        if (null === $this->parameterPartCollection) {
            return $query;
        }

        return $this->parameterPartCollection->bind($query);
    }
}

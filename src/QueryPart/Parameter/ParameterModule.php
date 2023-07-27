<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Parameter;

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

    protected function bindParameterParts(string $sql): string
    {
        if (null === $this->parameterPartCollection) {
            return $sql;
        }

        $patterns = [];
        $replacements = [];

        foreach ($this->parameterPartCollection->params as $param) {
            $patterns[] = $param->getKey();
            $replacements[] = $param->getValue();
        }

        return preg_replace($patterns, $replacements, $sql, 1);
    }
}

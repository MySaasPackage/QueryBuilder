<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Parameter;

use MySaasPackage\Support\QueryPart\StringablePart;

trait ParameterModule
{
    protected ParameterPartCollection|null $parameterPartCollection = null;

    protected function addParam(ParameterPart $param): self
    {
        $this->parameterPartCollection ??= new ParameterPartCollection();
        $this->parameterPartCollection->add($param);

        return $this;
    }

    public function setParameter(string|int $key, mixed $value): self
    {
        $this->addParam(new ParameterPart($key, new StringablePart($value)));

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
            $patterns[] = is_int($param->key) ? '/\?/' : sprintf('/:%s/', $param->key);
            $replacements[] = sprintf('\'%s\'', $param->value);
        }

        return preg_replace($patterns, $replacements, $sql, 1);
    }
}

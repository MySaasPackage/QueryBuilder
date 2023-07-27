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

    public function setParameter(string|int $key, $value): self
    {
        $this->addParam(new ParameterPart($key, $value));

        return $this;
    }

    protected function bindParameterParts(string $sql): string
    {
        if (null === $this->parameterPartCollection) {
            return $sql;
        }

        $params = $this->parameterPartCollection->params;

        $patterns = [];
        $replacements = [];

        foreach ($params as $param) {
            if (is_int($param->key)) {
                $patterns[] = '/\?/';
            } else {
                $patterns[] = sprintf('/:%s/', $param->key);
            }

            $replacements[] = strval($param->value);
        }

        return preg_replace($patterns, $replacements, $sql, 1);
    }
}

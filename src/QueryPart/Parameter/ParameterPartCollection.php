<?php

declare(strict_types=1);

namespace MySaasPackage\QueryPart\Parameter;

use Stringable;

class ParameterPartCollection
{
    public array $params = [];

    public function add(ParameterPart $param): static
    {
        $this->params[] = $param;

        return $this;
    }

    public function bind(Stringable|string $sql): Stringable|string
    {
        if (null === $this->params) {
            return $sql;
        }

        $patterns = [];
        $replacements = [];

        foreach ($this->params as $param) {
            $patterns[] = $param->getKey();
            $replacements[] = $param->getValue();
        }

        return preg_replace($patterns, $replacements, $sql, 1);
    }
}

<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

class ParameterPartCollection
{
    public function __construct(
        public array $params = []
    ) {
    }

    public function add(ParameterPart $param): self
    {
        $this->params[] = $param;

        return $this;
    }

    public function isNotEmpty(): bool
    {
        return 0 !== count($this->params);
    }
}

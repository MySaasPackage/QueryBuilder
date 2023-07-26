<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

class ParamsCollectionPart
{
    public function __construct(
        public array $params = []
    ) {
    }

    public function add(ParamPart $param): self
    {
        $this->params[] = $param;

        return $this;
    }

    public function isNotEmpty(): bool
    {
        return 0 !== count($this->params);
    }
}

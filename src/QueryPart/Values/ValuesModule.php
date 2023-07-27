<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Values;

trait ValuesModule
{
    protected ValuesPart|null $valuesPart = null;
    protected KeysPart|null $keysPart = null;

    public function values(array $values = []): self
    {
        $this->keysPart = new KeysPart(array_keys($values));
        $this->valuesPart = new ValuesPart(array_values($values));

        return $this;
    }

    public function __toKeys(): string
    {
        return $this->keysPart?->__toString() ?? '';
    }

    public function __toValues(): string
    {
        return $this->valuesPart?->__toString() ?? '';
    }
}

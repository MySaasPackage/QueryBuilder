<?php

declare(strict_types=1);

namespace MySaasPackage\QueryPart\KeyValue;

use MySaasPackage\QueryPart\Stringify;

trait KeyValueModule
{
    protected ValuesPart|null $valuesPart = null;
    protected KeysPart|null $keysPart = null;

    public function values(array $values = []): static
    {
        $this->keysPart = new KeysPart(array_map(fn ($value) => Stringify::stringify($value), array_keys($values)));
        $this->valuesPart = new ValuesPart(array_map(fn ($value) => Stringify::stringify($value), array_values($values)));

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

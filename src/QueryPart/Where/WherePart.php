<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Where;

use Stringable;

class WherePart implements Stringable
{
    public function __construct(
        public readonly Stringable|string $condition,
        public readonly Where|null $type = null
    ) {
    }

    public function __toString(): string
    {
        $sanitizedConditional = preg_replace('/"/', '\'', strval($this->condition));

        if (null === $this->type) {
            return $sanitizedConditional;
        }

        return sprintf('%s %s', $this->type->value, $sanitizedConditional);
    }
}

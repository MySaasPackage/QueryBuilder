<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Where;

use MySaasPackage\Support\QueryPart\QueryPart;

class WherePart implements QueryPart
{
    public function __construct(
        public readonly string $condition,
        public readonly Where|null $type = null
    ) {
    }

    public function __toString(): string
    {
        $sanitizedConditional = preg_replace('/"/', '\'', $this->condition);

        if (null === $this->type) {
            return $sanitizedConditional;
        }

        return sprintf('%s %s', $this->type->value, $sanitizedConditional);
    }
}

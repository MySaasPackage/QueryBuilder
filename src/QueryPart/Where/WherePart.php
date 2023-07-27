<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Where;

use Stringable;
use MySaasPackage\Support\QueryPart\StringablePart;

class WherePart implements Stringable
{
    public function __construct(
        public readonly StringablePart|string $condition,
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

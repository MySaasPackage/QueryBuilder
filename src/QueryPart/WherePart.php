<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

class WherePart implements Part
{
    public function __construct(
        public readonly string $condition,
        public readonly WhereType|null $type = null
    ) {
    }

    public function __toString(): string
    {
        $sanitized = preg_replace('/"/', '\'', $this->condition);

        if (null === $this->type) {
            return $sanitized;
        }

        return sprintf('%s %s', $this->type->value, $sanitized);
    }
}

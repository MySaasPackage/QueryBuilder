<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

class ReturningPart extends ColumnsPart
{
    public function __toString(): string
    {
        return 'RETURNING ' . parent::__toString();
    }
}

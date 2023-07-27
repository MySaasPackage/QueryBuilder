<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Returning;

use MySaasPackage\Support\QueryPart\Columns\ColumnsPart;

class ReturningPart extends ColumnsPart
{
    public function __toString(): string
    {
        return 'RETURNING ' . parent::__toString();
    }
}

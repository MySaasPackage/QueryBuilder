<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

interface DrivablePart
{
    public function __toPostgresSQL();

    public function __toMySQL();
}

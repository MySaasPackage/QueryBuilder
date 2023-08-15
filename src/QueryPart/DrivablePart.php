<?php

declare(strict_types=1);

namespace MySaasPackage\QueryPart;

interface DrivablePart
{
    public function __toPostgresSQL();

    public function __toMySQL();
}

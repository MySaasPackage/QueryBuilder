<?php

declare(strict_types=1);

namespace MySaasPackage\Support;

enum DbDriver: string
{
    case MySQL = 'mysql';
    case PostgreSQL = 'postgresql';
}

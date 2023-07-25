<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

enum OrderByType: string
{
    case Asc = 'ASC';
    case Desc = 'DESC';
}

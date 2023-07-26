<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\OrderBy;

enum OrderBy: string
{
    case ASC = 'ASC';
    case DESC = 'DESC';
}

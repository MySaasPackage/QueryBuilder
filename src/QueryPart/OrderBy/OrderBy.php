<?php

declare(strict_types=1);

namespace MySaasPackage\QueryPart\OrderBy;

enum OrderBy: string
{
    case ASC = 'ASC';
    case DESC = 'DESC';
}

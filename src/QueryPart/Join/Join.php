<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Join;

enum Join: string
{
    case JOIN = 'JOIN';
    case INNER_JOIN = 'INNER JOIN';
    case LEFT_JOIN = 'LEFT JOIN';
    case RIGHT_JOIN = 'RIGHT JOIN';
    case CROSS_JOIN = 'CROSS JOIN';
    case FULL_JOIN = 'FULL JOIN';
}

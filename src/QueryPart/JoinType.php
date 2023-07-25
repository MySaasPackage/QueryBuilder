<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

enum JoinType: string
{
    case Join = 'JOIN';
    case Inner = 'INNER JOIN';
    case Left = 'LEFT JOIN';
    case Right = 'RIGHT JOIN';
    case Cross = 'CROSS JOIN';
    case Full = 'FULL JOIN';
}

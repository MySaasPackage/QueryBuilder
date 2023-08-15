<?php

declare(strict_types=1);

namespace MySaasPackage\QueryPart\Join;

enum Join: string
{
    case Join = 'JOIN';
    case InnerJoin = 'INNER JOIN';
    case LeftJoin = 'LEFT JOIN';
    case RightJoin = 'RIGHT JOIN';
    case CrossJoin = 'CROSS JOIN';
    case FullJoin = 'FULL JOIN';
}

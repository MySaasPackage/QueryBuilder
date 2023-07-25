<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

enum WhereType: string
{
    case And = 'AND';
    case Or = 'OR';
}

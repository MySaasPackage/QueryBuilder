<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart;

enum Where: string
{
    case AND = 'AND';
    case OR = 'OR';
}

<?php

declare(strict_types=1);

namespace MySaasPackage\QueryPart\Where;

enum Where: string
{
    case AND = 'AND';
    case OR = 'OR';
}

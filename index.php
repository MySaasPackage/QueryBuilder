<?php

declare(strict_types=1);

$value = '1.0';

$isInteger = fn ($value) => is_int($value) || ctype_digit($value);

if ($isInteger($value)) {
    echo 'Integer';
} else {
    echo 'Not integer';
}

<?php

declare(strict_types=1);

$strings = [
    'SELECT * FROM table',
    '   SELECT * FROM table',
    'UPDATE table SE',
    'INSERT into VALUE',
    'DELETE FROM table',
    'COUNT(SELECT * FROM table)',
    'SELECT FROM (SELECT FROM)',
    ' anything here (SELECT FROM)',
];

$pattern = "/^(?:\s*)\b(SELECT|UPDATE|INSERT|DELETE)\b/i";

foreach ($strings as $str) {
    $pattern = "/^(?:\s*)\b(SELECT|UPDATE|INSERT|DELETE)\b/i";
    if (preg_match($pattern, $str)) {
        echo "\"$str\" matches the pattern\n";
    } else {
        echo "\"$str\" does not match the pattern\n";
    }
}

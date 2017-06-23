<?php

if (1) {
    echo 1;
}

function x(
    $a,
    $b
) {
    return $a * $b;
}

$f = function (
    $a,
    $b
) {
    return $a + $b;
};

try {
    $f(1, 2);
} catch (Exception $exception
) {
    echo $exception->getMessage();
}

class MyClass
{
}

function y($a, $b) {
    if ($a
    || $b) {
        return $a;
    } elseif ($a
        && $b
        ) {
        return $b;
    } elseif ($a
       xor $b
) {
        return $a - $b;
    } elseif ($a
         || $b) {
        return $a;
    } elseif ($a
           && $b) {
        return $b;
    }

    return $a + $b;
}

while (1
|| 2) {
    continue;
}

if ($a
&& ($b
|| $c)
&& preg_match(
    '/a/',
    'b'
)
) {
    return 12;
}

$a = 'string';
$v = $a{0};
$z = $a->{$v};

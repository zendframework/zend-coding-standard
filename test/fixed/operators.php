<?php

declare(strict_types=1);

$foo .= '';

$bar += Something::count();

$baz **= 2;

$quux |= Something::FOO;

if ($a === $b) {
    $foo = $bar ?? $a ?? $b;
} elseif ($a > $b) {
    $variable = $foo ? 'foo' : 'bar';
}

$foo = $_GET['foo'] ?? 'foo';

$bar = $bar ?? 'bar';

$bar = $bar['baz'] ?? 'baz';

if (isset($foo)) {
    $bar = $foo;
} else {
    $bar = 'foo';
}

$fooBar = isset($foo, $bar) ? 'foo' : 'bar';

$baz = ! isset($foo) ? 'foo' : 'baz';

echo Something::BAR;

if ($a
    && $b
) {
    echo 1;
}

$a
    ? $b
    : $c;

<?php

declare(strict_types=1);

$foo = $foo . '';

$bar = $bar + Something::count();

$baz = $baz ** 2;

$quux = $quux | Something::FOO;

if ($a === $b) {
    $foo = $bar ?? $a ?? $b;
} elseif ($a > $b) {
    $variable = $foo ? 'foo' : 'bar';
}

$foo = isset($_GET['foo']) ? $_GET['foo'] : 'foo';

$bar = isset($bar) ? $bar : 'bar';

$bar = isset($bar['baz']) ? $bar['baz'] : 'baz';

if (isset($foo)) {
    $bar = $foo;
} else {
    $bar = 'foo';
}

$fooBar = isset($foo, $bar) ? 'foo' : 'bar';

$baz = ! isset($foo) ? 'foo' : 'baz';

echo Something
    ::
    BAR;

if ($a &&
    $b) {
    echo 1;
}

$a ?
    $b :
    $c;

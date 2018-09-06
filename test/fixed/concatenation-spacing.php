<?php

declare(strict_types=1);

$string = 'foo' . 'bar';
$string = 'foo' . 'bar';
$string = 'foo' . 'bar';
$string = 'foo' . 'bar';

$foo    = 'foo';
$bar    = 'bar';
$string = $foo . $bar;
$string = $foo . $bar;
$string = $foo . $bar;
$string = $foo . $bar;

$string = 'foo' . $bar;
$string = 'foo' . $bar;
$string = 'foo' . $bar;
$string = 'foo' . $bar;

$string = $foo . 'bar';
$string = $foo . 'bar';
$string = $foo . 'bar';
$string = $foo . 'bar';
$string = $foo . 'bar';

$string = $foo . $bar . 'baz' . 'quux' . $string;

$string .=$foo;
$string .= $foo;
$string .=$foo;

$string = $foo .
    $bar .
    'baz'
    . $string;

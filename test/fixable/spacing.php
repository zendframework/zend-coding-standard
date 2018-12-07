<?php

declare(strict_types=1);

$closure = function ($x,$y) {
};

echo $closure,PHP_EOL;

new ArrayObject([],[]);

$a = [1,2,     3];

$b = [
    [1,  2,  3],
    [44, 55, 66],
];

$c = [
    'k1'      => [1,   2,   3],
    'longKey' => [111, 222, 333],
];

abstract class MyClassSpacing
{
    abstract public function method($x,$y,   $z);
}

$a1 = array_unique([1,  2],  [3,  4]);
$a2 = [[1,   2,   3]];

<?php

declare(strict_types=1);

$test = 1;

if (!$test > 0) {
    echo 1;
} elseif ( !$test === 0) {
    echo 0;
} else {
    echo 2;
}

while (    !    true) {

    echo 1;
    // comment

}

do {

    echo 1;

} while (    !    true);

new  DateTime();
new\DateTime();


class SingleLineBetweenMethods
{
    public function __construct()
    {
    }
    public function method()
    {
    }


    public function twoLines()
    {
    }
}

$a ++;
$b --;
-- $c;
++ $d;

function nullableTypes(? int $int, ? \Complex\Type $ct) : ? string
{
    return function (? bool $b) : ? float {
        return 0.0;
    };
}

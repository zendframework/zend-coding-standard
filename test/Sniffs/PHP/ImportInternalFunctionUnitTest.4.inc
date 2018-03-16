<?php

namespace ImportInternalFunction;

$arr = range(1, 10);
$count = \count($arr);

function () use ($arr) {
    $keys = array_keys($arr);
    $count = count($keys);

    return myFunction($arr);
}

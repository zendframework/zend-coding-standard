<?php

declare(strict_types=1);

$string = 'Hello'.$there.'. How are'.$you.$going.   "today $okay";
$string = 'Hello' . $there . '. How are' . $you . $going . "today $okay";
$string = 'Hello'.$there;
$string = 'Hello'. $there;
$string = 'Hello' .$there;

$foo = 'foo';
$bar    = 'bar';

$string  = $foo . $bar. 'baz' .'quux'.$string;
$string .= $foo;
$string .= $foo;
$string .= $foo;

$string = '1'
          . '2'
      . '3';

$string = '1' .
          '2' .
          '3';

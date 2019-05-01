<?php

declare(strict_types=1);

// valid
$there  = 'There';
$string = "Hello There\r\n";
$string = "Hello $there";
$string = 'Hello There';
$string = '\$var';
$query  = "SELECT * FROM table WHERE name =''";

// invalid
$string = "Hello There";
$string = "Hello" . " There" . "\n";
$string = "\$var";

<?php

declare(strict_types=1);

bar ( );
$foo->bar ( $arg1 );
Foo::bar ($arg2 , $arg3) ;

$foo->bar(
    $longArgument,
    $longerArgument,
    $muchLongerArgument
);

somefunction($foo, $bar, [
    // ...
], $baz);

$app->get ('/hello/{name}' , function ($name) use ($app) {

    return 'Hello ' . $app->escape($name);

});

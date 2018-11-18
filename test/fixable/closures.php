<?php

declare(strict_types=1);

$closureWithArgs = function( string $arg1, string $arg2 ) {
    // body
};

$closureWithArgsAndVars = function ( int $arg1 , int $arg2 ) use ( $var1, $var2 ): int {
    return $arg1 * $var1 + $arg2 * $var2;
};

static function ():void
{
}

static function ()    :    void
{
}

static function (
    int $a,
    int $c,
    int $d,
    int $e,
    int $b
):void {
}

static function (
    int $a,
    int $c,
    int $d,
    int $e,
    int $b
)   :   void {
}

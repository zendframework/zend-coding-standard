<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Fixed;

interface FooInterface
{
    public function bar() : void;

    public static function baz() : array;
}

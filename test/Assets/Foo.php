<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Fixed;

class Foo extends AbstractFoo implements FooInterface
{
    public function bar() : void
    {
    }

    public static function baz() : array
    {
        return [];
    }
}

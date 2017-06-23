<?php

namespace Foo;

use const D;
use const C;
use B;
use A;
use function Y;
use function X;

class J {
    use MyTrait;

    public function closure($foo)
    {
        return function ($x) use ($foo) {
            return $x <=> $foo;
        };
    }

    public function anonym()
    {
        return new class() {
            use AnotherTrait;
        };
    }
}

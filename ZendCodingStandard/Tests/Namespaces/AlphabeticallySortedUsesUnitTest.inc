<?php

namespace Foo {
    use const D;
    use const C;
    use B;
    use A;
    use function Y;
    use function X;

    $foo = 1;
    $closure = function ($x) use ($foo) {
    };
}

namespace Bar {
    use A;
    use function B;
    use const C;
    use D;
    use function E;
    use const F;

    class J {
        use MyTrait;
    }
}

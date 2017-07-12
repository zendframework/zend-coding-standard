<?php

namespace A {
    $arr = \range(1, 10);
    $count = Count($arr);
}

namespace B {
    use function \range;

    $arr = \range(1, 10);
    $count = \count($arr);
}

namespace C {
    use function Range;

    $arr = \range(1, 10);
    $count = count($arr);
}

namespace D {
    use function range;

    $arr = Range(1, 10);
    $count = \Count($arr);
}

namespace E {
    use function A\count;

    $arr = range(1, 10);
    $countA = count($arr);
    $countFQN = \A\count($arr);
    $countGlob = \count($arr);
}

namespace F {
    use function array_keys as count;

    $arr = range(1, 10);
    $countImpoted = count($arr);
    $countGlob = \count($arr);
}

namespace G {
    use DateTime;

    $arr = range(1, 10);
}

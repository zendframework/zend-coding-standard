<?php

namespace A {
    $a = \E_NOTICE;
    $b = E_WARNING;
}

namespace B {
    use const \E_NOTICE;

    $a = \E_NOTICE;
    $b = \E_WARNING;
}

namespace C {
    use function in_array;

    $x = in_array(E_ALL, [], true);
}

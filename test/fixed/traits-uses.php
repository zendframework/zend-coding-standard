<?php

declare(strict_types=1);

namespace TraitsUses;

class Foo
{
    use FirstTrait;
}

class Bar
{
    use FirstTrait;
    use FourthTrait;
    use SecondTrait;
    use ThirdTrait {
        x as public;
    }

    public function __construct()
    {
    }
}

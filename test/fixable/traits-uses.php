<?php

declare(strict_types=1);

namespace TraitsUses;

class Foo
{
    use FirstTrait;

}

class Bar
{
    use FirstTrait, SecondTrait;

    use ThirdTrait {
        x as public;
    }

    use FourthTrait;
    public function __construct()
    {
    }
}

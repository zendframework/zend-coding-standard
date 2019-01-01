<?php

declare(strict_types=1);

namespace FooBar;

use ArrayObject as AO;
use DateTime;

class Foo
{
    public $bar;
}

trait BarTrait
{
    public $var;
}

interface BazInterface
{
    public function big() : int;
}

new DateTime();
new AO();
new AO();

<?php

declare(strict_types = 1);

namespace Vendor\Package;

use FooClass;
use BarClass as Bar;
use OtherVendor\OtherPackage\BazClass;

class ClassName extends BazClass implements \ArrayAccess, \Countable
{
    public function __construct(FooClass $foo, Bar $bar)
    {
        // ...
    }
}

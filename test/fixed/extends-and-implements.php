<?php

declare(strict_types=1);

namespace Vendor\Package;

use ArrayAccess;
use BarClass as Bar;
use Countable;
use FooClass;
use OtherVendor\OtherPackage\BazClass;

class ClassName extends BazClass implements ArrayAccess, Countable
{
    public function __construct(FooClass $foo, Bar $bar)
    {
        // ...
    }
}

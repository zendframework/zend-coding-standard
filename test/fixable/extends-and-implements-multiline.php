<?php

declare(strict_types=1);

namespace Vendor\Package;

use ParentClass;
use BarClass as Bar;
use OtherVendor\OtherPackage\BazClass;
use ArrayAccess;
use Countable;
use Serializable;

class ClassNameMultiline extends ParentClass implements
    \ArrayAccess,
    \Countable,
    \Serializable
{
    // constants, properties, methods
}

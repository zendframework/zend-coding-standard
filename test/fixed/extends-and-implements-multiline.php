<?php

declare(strict_types=1);

namespace Vendor\Package;

use ArrayAccess;
use Countable;
use ParentClass;
use Serializable;

class ClassName extends ParentClass implements
    ArrayAccess,
    Countable,
    Serializable
{
    // constants, properties, methods
}

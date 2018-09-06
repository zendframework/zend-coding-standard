<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Fixed;

use stdClass;

use function get_called_class;
use function get_class;
use function get_parent_class;

class ClassReferences
{
    /**
     * @return string[]
     */
    public function names() : iterable
    {
        yield __CLASS__;
        yield get_class();
        yield get_class($this);
        yield get_class(new stdClass());
        yield get_parent_class();
        yield get_called_class();
        yield static::class;
    }
}

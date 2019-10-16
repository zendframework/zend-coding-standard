<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\fixed;

use ArrayAccess;
use Countable;
use Foo;
use HandleableInterface;
use Serializable;

class AnonymousClasses
{
    public function test(): void
    {
        // Anonymous Classes MUST follow the same guidelines and principles as
        // closures.

        $instance = new class {
            // Class content
        };

        // The opening brace MAY be on the same line as the class keyword so long
        // as the list of implements interfaces does not wrap. If the list of
        // interfaces wraps, the brace MUST be placed on the line immediately
        // following the last interface.

        // Brace on the same line
        $instance = new class extends Foo implements HandleableInterface {
            // Class content
        };

        // Brace on the next line
        $instance = new class extends Foo implements
            ArrayAccess,
            Countable,
            Serializable
        {
            // Class content
        };
    }
}

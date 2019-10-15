<?php

namespace ZendCodingStandardTest\fixed;

class Operators
{
    public function testMultipleStatementAlignment(): void
    {
        // There must be at least one space on either side of an equals sign
        // used to assign a value to a variable. In case of a block of related
        // assignments, more space MUST be inserted before the equal sign to
        // promote readability.

        $foo        = 'bar';
        $fooBar= 'bar';
    }
}

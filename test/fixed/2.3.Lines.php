<?php

namespace ZendCodingStandardTest\fixed;

class Lines
{
    public function testLineLength(): void
    {
        // There MUST NOT be a hard limit on line length.
        //
        // The soft limit on line length MUST be 120 characters.
        //
        // Lines SHOULD NOT be longer than 80 characters; lines longer than
        // that SHOULD be split into multiple subsequent lines of no more than
        // 80 characters each.
    }

    public function testTrailingWhitespace(): void
    {
        // There MUST NOT be trailing whitespace at the end of lines.
        //
        // Blank lines MAY be added to improve readability and to indicate
        // related blocks of code except where explicitly forbidden.

        $foo = 'bar';
    }

    public function testDisallowMultipleStatements(): void
    {
        // There MUST NOT be more than one statement per line.

        $foo = 'bar';
        $bar = 'foo';
    }
}

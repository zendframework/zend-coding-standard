<?php

declare(strict_types=1);

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

    public function testThereMayBeMaximumOneBlankLine(): void
    {
        // There MAY be maximum one blank line to improve readability and to
        // indicate related blocks of code except where explicitly forbidden.

        $x = 1;

        $y = 1;
    }

    public function testThereMayNotBeAnyBlankLineFfterOpeningBracesAndBeforeClosingBrace(): void
    {
        // There MAY NOT be any blank line after opening braces and before
        // closing braces.

        $noBlankLine = function () use ($noBlankLine) {
            $noBlankLine = 1;
        };

        $closure = function () {
            $noBlankLine = 1;
        };

        if ($x) {
            while (true) {
                foreach ($arr as $elem) {
                    do {
                        $stop = 1;
                    } while (true);
                }
            }
        }

        switch (true) {
            case 1:
                break;
        }
    }
}

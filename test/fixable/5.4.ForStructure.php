<?php

namespace ZendCodingStandardTest\fixed;

class ForStructure
{
    public function testForStructure(): void
    {
        for( $i = 0; $i < 10; $i++ )
        {
            echo $i;
        }
    }

    public function testMultilineForStructure(): void
    {
        // Expressions in parentheses MAY be split across multiple lines, where
        // each subsequent line is indented at least once. When doing so, the
        // first expression MUST be on the next line. The closing parenthesis and
        // opening brace MUST be placed together on their own line with one space
        // between them.

        for (
            $i = 0;
            $i < 10;
            $i++
        ) {
            echo $i;
        }
    }
}

<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\fixed;

class WhileAndDoWhile
{
    public function testWhileStructure(): void
    {
        while( $expr ){
            $expr = false;
        }
    }

    public function testMultilineWhileStructure(): void
    {
        // Expressions in parentheses MAY be split across multiple lines, where
        // each subsequent line is indented at least once. When doing so, the
        // first condition MUST be on the next line. The closing parenthesis and
        // opening brace MUST be placed together on their own line with one space
        // between them. Boolean operators between conditions MUST always be at
        // the beginning or at the end of the line, not a mix of both.

        while ($expr1
            && $expr2
        )
        {
            $expr1 = $expr2 = false;
        }
    }

    public function testDoWhileStructure(): void
    {
        do { $expr = false; } while ( $expr );
    }

    public function testMultilineDoWhileStructure(): void
    {
        // Expressions in parentheses MAY be split across multiple lines, where
        // each subsequent line is indented at least once. When doing so, the
        // first condition MUST be on the next line. Boolean operators between
        // conditions MUST always be at the beginning or at the end of the line,
        // not a mix of both.

        do {
            $expr1 = $expr2 = false;
        } while (
            $expr1
            && $expr2
        );
    }
}

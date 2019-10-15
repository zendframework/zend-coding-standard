<?php

namespace ZendCodingStandardTest\fixed;

class IfElseifElse
{
    public function testIfStructure(?string $expr1, ?string $expr2): ?string
    {
        // The else and elseif MUST be on the same line as the closing brace
        // from the earlier body.
        //
        // The keyword elseif SHOULD be used instead of else if so that all
        // control keywords look like single words.

        if ( $expr1 ) {
            return $expr1;
        }
        else if ( $expr2 )
        {
            return $expr2;
        }
        else
        {
            return null;
        }
    }

    public function testMultilineIfStructure(?string $expr1, ?string $expr2): ?string
    {
        // Expressions in parentheses MAY be split across multiple lines, where
        // each subsequent line is indented at least once. When doing so, the
        // first condition MUST be on the next line. The closing parenthesis and
        // opening brace MUST be placed together on their own line with one space
        // between them. Boolean operators between conditions MUST always be at
        // the beginning or at the end of the line, not a mix of both.

        if ($expr1
            && $expr2)
        {
            return $expr1;
        } elseif (
            $expr3
            && $expr4 ) {
            return $expr2;
        }

        return null;
    }
}

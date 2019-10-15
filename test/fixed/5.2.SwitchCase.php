<?php

namespace ZendCodingStandardTest\fixed;

class SwitchCase
{
    public function testSwitchStructure(?int $expr): void
    {
        // The case statement MUST be indented once from switch, and the break
        // keyword (or other terminating keywords) MUST be indented at the same
        // level as the case body. There MUST be a comment such as
        // `// no break` when fall-through is intentional in a non-empty case
        // body.

        switch ($expr) {
            case 0:
                echo 'First case, with a break';
                break;
            case 1:
                echo 'Second case, which falls through';
            // no break
            case 2:
            case 3:
            case 4:
                echo 'Third case, return instead of break';
                return;
            default:
                echo 'Default case';
                break;
        }
    }

    public function testMultilineSwitchStructure(): void
    {
        // Expressions in parentheses MAY be split across multiple lines, where
        // each subsequent line is indented at least once. When doing so, the
        // first condition MUST be on the next line. The closing parenthesis and
        // opening brace MUST be placed together on their own line with one space
        // between them. Boolean operators between conditions MUST always be at
        // the beginning or at the end of the line, not a mix of both.

        switch (
            $expr1
            && $expr2
        ) {
            case 0:
                echo 'First case, with a break';
                break;
            default:
                echo 'Default case';
                break;
        }
    }
}

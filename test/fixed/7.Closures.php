<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\fixed;

class Closures
{
    public function testClosures(): void
    {
        // Closures MUST be declared with a space after the function keyword, and
        // a space before and after the use keyword.
        //
        // The opening brace MUST go on the same line, and the closing brace MUST
        // go on the next line following the body.
        //
        // There MUST NOT be a space after the opening parenthesis of the argument
        // list or variable list, and there MUST NOT be a space before the closing
        // parenthesis of the argument list or variable list.
        //
        // In the argument list and variable list, there MUST NOT be a space
        // before each comma, and there MUST be one space after each comma.
        // Closure arguments with default values MUST go at the end of the
        // argument list.
        //
        // Argument lists and variable lists MAY be split across multiple lines,
        // where each subsequent line is indented once. When doing so, the first
        // item in the list MUST be on the next line, and there MUST be only one
        // argument or variable per line.
        //
        // When the ending list (whether of arguments or variables) is split
        // across multiple lines, the closing parenthesis and opening brace MUST
        // be placed together on their own line with one space between them.
        //
        // If a return type is present, it MUST follow the same rules as with
        // normal functions and methods; if the use keyword is present, the colon
        // MUST follow the use list closing parentheses with no spaces between
        // the two characters.

        $closureWithArgs = function ($arg1, $arg2) {
            echo "$arg1, $arg2";
        };

        $closureWithArgsAndVars = function ($arg1, $arg2) use ($var1, $var2) {
            echo "$arg1, $arg2, $var1, $var2";
        };

        $closureWithArgsVarsAndReturn = function ($arg1, $arg2) use ($var1, $var2): bool {
            return $arg1 === $arg2 && $var1 === $var2;
        };

        $multiLineCLosre = function (
            $arg1,
            $arg2
        ) use (
            $var1,
            $var2
        ): bool {
            return $arg1 === $arg2 && $var1 === $var2;
        };
    }

    public function testInheritedVariablesMustBeUsed(): void
    {
        // Inherited variables passed via `use` MUST be used in closures.

        $message = 'world';
        $example = function ($arg) use ($message) {
            echo "$arg $message";
        };
        $example('hello');
    }
}

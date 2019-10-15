<?php

namespace ZendCodingStandardTest\fixed;

class MethodsAndFunctions
{
    public function testMethodsAndFunctions ($arg1, &$arg2, $arg3 = []) {
        // Visibility MUST be declared on all methods.
        //
        // Method names MUST NOT be prefixed with a single underscore to indicate
        // protected or private visibility. That is, an underscore prefix
        // explicitly has no meaning.
        //
        // Method and function names MUST NOT be declared with space after the
        // method name. The opening brace MUST go on its own line, and the
        // closing brace MUST go on the next line following the body. There MUST
        // NOT be a space after the opening parenthesis, and there MUST NOT be a
        // space before the closing parenthesis.

        function fooBar ( $arg1, &$arg2, $arg3 = [] ) {
            // function body
        }

    }




    private static function testOneSingleLineBetweenMethods()
    {
        // There MUST be a single empty line between methods in a class.
    }
}

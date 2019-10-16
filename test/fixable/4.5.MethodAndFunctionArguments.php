<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\fixed;

use ArrayObject;

use function array_unique;
use function func_get_args;

class MethodAndFunctionArguments
{
    public function testFunctionDeclarationArgumentSpacing(int $arg1  ,  & $arg2,$arg3): void
    {
        // In the argument list, there MUST NOT be a space before each comma, and
        // there MUST be one space after each comma.
        //
        // When using the reference operator & before an argument, there MUST NOT
        // be a space after it.

        new ArrayObject( $a  ,  $b   ,$c);

        $array1 = [$x   ,   $y  ,  $z];
        $array2 = [[1 , 2 , 3]];

        $closure = function ($arg1  ,$arg2) {
            return array_unique([$arg1,$arg2]  ,  [3  ,4]);
        };
    }

    public function testValidDefaultValue(int $arg1, &$arg2, $arg3 = []): string
    {
        // Method and function arguments with default values MUST go at the end
        // of the argument list.

        return 'foo';
    }

    public function testMultiLineFunctionDeclaration(
        string $foo, string $bar,
        int $baz
    ): ?string
    {
        // Argument lists MAY be split across multiple lines, where each
        // subsequent line is indented once. When doing so, the first item in the
        // list MUST be on the next line, and there MUST be only one argument per
        // line. When the argument list is split across multiple lines, the
        // closing parenthesis and opening brace MUST be placed together on their
        // own line with one space between them.
        //
        // When you have a return type declaration present, there MUST be one
        // space after the colon followed by the type declaration. The colon and
        // declaration MUST be on the same line as the argument list closing
        // parenthesis with no spaces between the two characters.

        return 'foo';
    }

    public function testNullableTypeDeclaration(? string $arg1, int &$arg2 = null): void
    {
        // In nullable type declarations, there MUST NOT be a space between the
        // question mark and the type.
        //
        // The question mark MUST be used when a the default argument value is
        // null.
    }

    public function testVariadicThreeDotOperator(string $foo, & ... $baz): void
    {
        // There MUST NOT be a space between the variadic three dot operator and
        // the argument name.
        //
        // When combining both the reference operator and the variadic three dot
        // operator, there MUST NOT be any space between the two of them.

        function method($a, $b, ... $c) {
            if ($a < $b) {
                method(... func_get_args());
            }
        }
    }

    public function testReferencing(): void
    {
        function ($x, & $y, string & $z, int&$v) {
            return $z & $v === $x & $y;
        }

        $a = &new ArrayObject();
        $b = & new ArrayObject();
        $c =& new ArrayObject();
        $d =&new ArrayObject();
    }
}

<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\fixed;

class MethodAndFunctionCalls
{
    public function testFunctionCalls(): void
    {
        // When making a method or function call, there MUST NOT be a space
        // between the method or function name and the opening parenthesis, there
        // MUST NOT be a space after the opening parenthesis, and there MUST NOT
        // be a space before the closing parenthesis. In the argument list, there
        // MUST NOT be a space before each comma, and there MUST be one space
        // after each comma.

        bar();
        $foo->bar( $arg1 );
        Foo::bar(  $arg2  ,  $arg3  );
    }

    public function testArguments(): void
    {
        // Argument lists MAY be split across multiple lines, where each
        // subsequent line is indented once. When doing so, the first item in the
        // list MUST be on the next line, and there MUST be only one argument per
        // line. A single argument being split across multiple lines (as might be
        // the case with an anonymous function or array) does not constitute
        // splitting the argument list itself.

        $foo->bar(
            $longArgument, $longerArgument, $muchLongerArgument
        );

        $app->get('/hello/{name}', function ($name) use ($app) {
            return 'Hello ' . $app->escape($name);
        });
    }
}

<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\fixed;

class Operators
{
    public function testMultipleStatementAlignment(): void
    {
        // There must be at least one space on either side of an equals sign
        // used to assign a value to a variable. In case of a block of related
        // assignments, more space MUST be inserted before the equal sign to
        // promote readability.

        $foo =      'bar';
        $fooBar=  'bar';
    }

    public function testOperatorSpacing(): void
    {
        // There MUST be one single whitespace around logical operators.

        if ($foo || $bar) {
            return;
        }
        if ($foo||$bar && $baz) {
            return;
        }
        if ($foo|| $bar&&$baz) {
            return;
        }
        if ($foo  ||   $bar   && $baz) {
            return;
        }

        $result = 1 + 2;
        $result = 1  +   2;
        $result = 1+2;

        $result = 1 - 2;
        $result = 1  -   2;
        $result = 1-2;

        $result = 1 * 2;
        $result = 1  *   2;
        $result = 1*2;

        $result = 1 / 2;
        $result = 1  /   2;
        $result = 1/2;

        $result = 1 % 2;
        $result = 1  %   2;
        $result = 1%2;
        $result = '100%';

        $result += 4;
        $result+=4;
        $result -= 4;
        $result-=4;
        $result /= 4;
        $result/=4;
        $result *=4;
        $result*=4;
    }

    public function testObjectOperatorSpacing()
    {
        // There MAY NOT be any white space around the object operator unless
        // multilines are used.

        $this->testOperatorSpacing();
        $this-> testOperatorSpacing();
        $this    ->  testOperatorSpacing();
        $this
            ->testOperatorSpacing();
        $this->
            testOperatorSpacing();
    }


    public function testUseStrictComparisonOperators(): void
    {
        // Loose comparison operators SHOULD NOT be used, use strict comparison
        // operators instead.

        $foo == 123;
        123 == $foo;
        true != 0.0;
        false <> true;
    }

    public function testUseNullCoalesceOperator(): void
    {
        // The null coalesce operator MUST be used when possible.

        $a = isset($_GET['a']) ? $_GET['a'] : 'a';
        $b = isset($bb) ? $bb : 'bb';
        $c = isset($cc['c']) ? $cc['c'] : 'c';
    }

    public function testAssignmentOperators(int $var): void
    {
        // Assignment operators SHOULD be used when possible.

        $var = $var & 2;
        $var = $var | 4;
        $var = $var . '';
        $var = $var / 10;
        $var = $var - 100;
        $var = $var ** 2;
        $var = $var % 2;
        $var = $var * 1000;
        $var = $var + 4;
        $var = $var << 2;
        $var = $var >> 2;
        $var = $var ^ 10;
        $var = $var + 10;
    }
}

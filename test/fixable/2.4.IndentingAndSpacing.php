<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\fixed;

class IndentingAndSpacing
{
    public function testWhiteSpaceScopeIndent(): void
    {
        // Code MUST use an indent of 4 spaces for each indent level, and MUST
        // NOT use tabs for indenting.

      $foo = 'bar';
    }

    public function testEncapsedStrings(): void
    {
        $string = "Hello There\n";
        $string = "Hello $there";
    }

    public function testConcatenationSpacing(): void
    {
        $string = 'Hello'.$there.'. How are'.$you.$doing.   "today $okay";
        $string = 'Hello' . $there . '. How are' . $you . $doing . "today $okay";
        $string = 'Hello'.$there;
        $string = 'Hello'. $there;
        $string = 'Hello' .$there;

        $foo = 'foo';
        $bar    = 'bar';

        $string  = $foo . $bar. 'baz' .'quux'.$string;
        $string .= $foo;
        $string .= $foo;
        $string .= $foo;

        $string = '1'
            . '2'
            . '3';

        $string = '1' .
            '2' .
            '3';
    }

    public function testUnnecessaryStringConcat(): void
    {
        $x = 'My '.'string';
        $x = 'My '. 1234;
        $x = 'My '.$y.' test';
    }
}

<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\fixed;

class Arrays
{
    public function testArrayFormatting(): void
    {
        // The short array syntax MUST be used to define arrays.
        //
        // All values in multiline arrays must be indented with 4 spaces.
        //
        // All array values must be followed by a comma, including the last value.
        //
        // There MUST be no whitespace around the opening bracket or before the
        // closing bracket when referencing an array.
        //
        // All double arrow symbols MUST be aligned to one space after the
        // longest array key.

        $array1 = [
            'key1'      => 'value1',
            'key2'      => 'value2',
            'keyTwenty' => 'value3',
        ];

        $array2 = [
            'one'    => function () {
                $foo    = [1, 2, 3];
                $barBar = [
                    1,
                    2,
                    3,
                ];
            },
            'longer' => 2,
            3        => 'three',
        ];
    }

    public function testShortListSyntax(): void
    {
        // The short list syntax `[...]` SHOULD be used instead of `list(...)`.

        [$a, $b, $c] = [1, 2, 3];

        ["a" => $a, "b" => $b, "c" => $c] = ["a" => 1, "b" => 2, "c" => 3];

        [$a, $b] = [$b, $a];
    }
}

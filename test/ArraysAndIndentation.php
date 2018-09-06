<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Fixed;

class ArraysAndIndentation
{
    public function array() : array
    {
        return [
            0,
            1,
            2,
            3,
            4,
            5,
        ];
    }

    public function singleLineArray() : array
    {
        return [1 => 2];
    }

    public function multiArray() : array
    {
        return [
            '0' => [
                '2' => 2,
                1   => '1',
            ],
            4   => [
                7,
                8,
                9,
            ],
        ];
    }
}

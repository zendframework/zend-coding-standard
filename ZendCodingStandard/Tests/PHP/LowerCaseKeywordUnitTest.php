<?php
namespace ZendCodingStandard\Tests\PHP;

use ZendCodingStandard\Tests\TestCase;

class LowerCaseKeywordUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
        return [
            3 => 1,
            5 => 1,
            7 => 2,
            8 => 1,
            9 => 1,
            11 => 1,
            13 => 3,
            15 => 1,
            16 => 1,
            18 => 1,
            19 => 1,
            21 => 3,
            22 => 4,
            23 => 1,
            27 => 3,
            29 => 3,
            31 => 1,
            34 => 1,
            35 => 1,
            37 => 1,
            38 => 1,
            41 => 1,
            42 => 1,
            43 => 1,
            44 => 1,
            45 => 1,
            46 => 1,
            47 => 2,
            50 => 1,
            51 => 2,
            53 => 1,
            54 => 1,
            57 => 3,
            59 => 1,
            60 => 1,
            61 => 2,
            62 => 1,
            63 => 1,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

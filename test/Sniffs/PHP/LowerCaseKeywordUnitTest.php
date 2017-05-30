<?php
namespace ZendCodingStandardTest\Sniffs\PHP;

use ZendCodingStandardTest\Sniffs\TestCase;

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
            17 => 2,
            18 => 2,
            20 => 1,
            21 => 1,
            23 => 3,
            24 => 4,
            25 => 1,
            29 => 3,
            31 => 3,
            33 => 1,
            36 => 1,
            37 => 1,
            39 => 1,
            40 => 1,
            43 => 1,
            44 => 1,
            45 => 1,
            46 => 1,
            47 => 1,
            48 => 1,
            49 => 2,
            52 => 1,
            53 => 2,
            55 => 1,
            56 => 1,
            59 => 3,
            61 => 1,
            62 => 1,
            63 => 2,
            64 => 1,
            65 => 1,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

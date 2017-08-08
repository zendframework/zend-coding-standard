<?php
namespace ZendCodingStandardTest\Sniffs\Formatting;

use ZendCodingStandardTest\Sniffs\TestCase;

class ReturnTypeUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
        if ($testFile === 'ReturnTypeUnitTest.1.inc') {
            return [
                6 => 1,
                7 => 3,
                9 => 2,
                10 => 2,
                17 => 2,
                18 => 3,
                20 => 1,
                21 => 2,
                22 => 1,
            ];
        }

        return [
            8 => 1,
            12 => 1,
            30 => 2,
            39 => 1,
            45 => 2,
            53 => 2,
            58 => 2,
            59 => 2,
            60 => 2,
            61 => 3,
            62 => 3,
            63 => 2,
            64 => 1,
            65 => 2,
            66 => 1,
            67 => 2,
            68 => 1,
            69 => 2,
            70 => 1,
            71 => 2,
            72 => 1,
            73 => 2,
            74 => 1,
            75 => 2,
            76 => 1,
            77 => 2,
            78 => 1,
            80 => 2,
            81 => 1,
            83 => 1,
            84 => 3,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

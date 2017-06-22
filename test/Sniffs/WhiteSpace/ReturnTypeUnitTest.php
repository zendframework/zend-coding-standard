<?php
namespace ZendCodingStandardTest\Sniffs\WhiteSpace;

use ZendCodingStandardTest\Sniffs\TestCase;

class ReturnTypeUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
        return [
            5 => 1,
            9 => 1,
            27 => 2,
            36 => 1,
            42 => 2,
            50 => 2,
            55 => 2,
            56 => 2,
            57 => 2,
            58 => 3,
            59 => 3,
            60 => 2,
            61 => 1,
            62 => 2,
            63 => 1,
            64 => 2,
            65 => 1,
            66 => 2,
            67 => 1,
            68 => 2,
            69 => 1,
            70 => 2,
            71 => 1,
            72 => 2,
            73 => 1,
            74 => 2,
            75 => 1,
            77 => 2,
            78 => 1,
            80 => 1,
            81 => 3,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

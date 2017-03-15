<?php
namespace ZendCodingStandard\Tests\Arrays;

use ZendCodingStandard\Tests\TestCase;

class FormatUnitTest extends TestCase
{
    public function getErrorList()
    {
        return [
            2 => 2,
            4 => 1,
            5 => 1,
            // 6 => 1,
            7 => 1,
            11 => 1,
            // 12 => 1,
            14 => 2,
            15 => 1,
            16 => 2,
            19 => 1,
            20 => 1,
            22 => 1,
            25 => 1,
            31 => 3,
            33 => 1,
            38 => 2,
            39 => 1,
            40 => 1,
            47 => 2,
            49 => 1,
            53 => 2,
            55 => 1,
            56 => 2,
            62 => 2,
            63 => 1,
            68 => 2,
            69 => 2,
            74 => 1,
            75 => 1,
            76 => 1,
        ];
    }

    public function getWarningList()
    {
        return [];
    }
}

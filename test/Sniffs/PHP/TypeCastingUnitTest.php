<?php
namespace ZendCodingStandardTest\Sniffs\PHP;

use ZendCodingStandardTest\Sniffs\TestCase;

class TypeCastingUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
        return [
            3 => 1,
            5 => 1,
            7 => 1,
            8 => 1,
            17 => 1,
            19 => 1,
            20 => 1,
            29 => 1,
            31 => 1,
            32 => 1,
            33 => 1,
            35 => 1,
            36 => 1,
            37 => 1,
            39 => 1,
            40 => 1,
            41 => 1,
            42 => 1,
            43 => 1,
            44 => 1,
            45 => 1,
            46 => 1,
            47 => 1,
            48 => 1,
            49 => 1,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

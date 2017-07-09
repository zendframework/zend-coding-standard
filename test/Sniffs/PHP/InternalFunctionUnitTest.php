<?php
namespace ZendCodingStandardTest\Sniffs\PHP;

use ZendCodingStandardTest\Sniffs\TestCase;

class InternalFunctionUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
        switch ($testFile) {
            case 'InternalFunctionUnitTest.1.inc':
                return [
                    4 => 1,
                    5 => 1,
                    11 => 1,
                    12 => 1,
                    18 => 1,
                    19 => 1,
                    26 => 1,
                    32 => 1,
                    41 => 1,
                    49 => 1,
                ];
            case 'InternalFunctionUnitTest.2.inc':
                return [
                    5 => 1,
                    6 => 1,
                    8 => 1,
                    9 => 1,
                ];
        }

        return [
            3 => 1,
            5 => 1,
            9 => 1,
            19 => 1,
            22 => 1,
            23 => 1,
            25 => 1,
            27 => 1,
            28 => 1,
            30 => 1,
            31 => 1,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

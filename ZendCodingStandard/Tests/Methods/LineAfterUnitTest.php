<?php
namespace ZendCodingStandard\Tests\Methods;

use ZendCodingStandard\Tests\TestCase;

class LineAfterUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
        switch ($testFile) {
            case 'LineAfterUnitTest.1.inc':
                return [
                    5 => 1,
                    8 => 1,
                    13 => 1,
                    14 => 2,
                    15 => 1,
                ];
            case 'LineAfterUnitTest.2.inc':
                return [
                    7 => 1,
                    10 => 1,
                    15 => 1,
                    20 => 1,
                    21 => 2,
                    22 => 1,
                    24 => 1,
                ];
        }

        return [
            7 => 1,
            10 => 1,
            15 => 1,
            20 => 1,
            21 => 2,
            22 => 1,
            24 => 1,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

<?php
namespace ZendCodingStandardTest\Sniffs\ControlStructures;

use ZendCodingStandardTest\Sniffs\TestCase;

class MultilineBraceUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
        return [
            24 => 1,
            34 => 1,
            38 => 1,
            41 => 1,
            42 => 1,
            45 => 1,
            48 => 1,
            56 => 1,
            61 => 1,
            62 => 1,
            63 => 1,
            64 => 1,
            65 => 1,
            66 => 1,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

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
            74 => 1,
            81 => 1,
            87 => 1,
            89 => 1,
            97 => 1,
            98 => 1,
            106 => 1,
            111 => 1,
            116 => 1,
            117 => 1,
            128 => 1,
            136 => 1,
            138 => 1,
            146 => 1,
            149 => 1,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

<?php
namespace ZendCodingStandard\Tests\WhiteSpace;

use ZendCodingStandard\Tests\TestCase;

class NoBlankLineAtStartUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
        return [
            3 => 1,
            9 => 1,
            17 => 1,
            19 => 1,
            30 => 1,
            36 => 1,
            42 => 1,
            45 => 1,
            47 => 1,
            54 => 1,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

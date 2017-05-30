<?php
namespace ZendCodingStandardTest\Sniffs\WhiteSpace;

use ZendCodingStandardTest\Sniffs\TestCase;

class BlankLineUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
        return [
            2 => 1,
            6 => 1,
            10 => 1,
            11 => 1,
            18 => 1,
            26 => 1,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

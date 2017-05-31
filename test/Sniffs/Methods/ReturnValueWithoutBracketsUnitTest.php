<?php
namespace ZendCodingStandardTest\Sniffs\Methods;

use ZendCodingStandardTest\Sniffs\TestCase;

class ReturnValueWithoutBracketsUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
        return [
            7 => 1,
            12 => 1,
            22 => 1,
            23 => 1,
            30 => 1,
            36 => 1,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

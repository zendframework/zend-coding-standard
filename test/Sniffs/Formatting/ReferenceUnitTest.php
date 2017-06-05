<?php
namespace ZendCodingStandardTest\Sniffs\Formatting;

use ZendCodingStandardTest\Sniffs\TestCase;

class ReferenceUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
        return [
            3 => 1,
            5 => 1,
            6 => 2,
            7 => 1,
            8 => 1,
            13 => 1,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

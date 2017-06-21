<?php
namespace ZendCodingStandardTest\Sniffs\Operators;

use ZendCodingStandardTest\Sniffs\TestCase;

class LogicalOperatorNotAtTheEndOfTheLineUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
        return [
            3 => 1,
            6 => 1,
            9 => 1,
            12 => 1,
            15 => 1,
            16 => 1,
            19 => 1,
            27 => 1,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

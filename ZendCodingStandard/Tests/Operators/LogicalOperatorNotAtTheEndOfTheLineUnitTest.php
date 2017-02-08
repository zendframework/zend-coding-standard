<?php
namespace ZendCodingStandard\Tests\Operators;

use ZendCodingStandard\Tests\TestCase;

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
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

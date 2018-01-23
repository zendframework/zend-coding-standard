<?php
namespace ZendCodingStandardTest\Sniffs\Classes;

use ZendCodingStandardTest\Sniffs\TestCase;

class NoNullValuesUnitTest extends TestCase
{
    /**
     * @param string $testFile
     * @return int[]
     */
    public function getErrorList($testFile = '')
    {
        return [
            6 => 1,
            7 => 1,
            9 => 1,
            11 => 1,
            13 => 1,
            23 => 1,
        ];
    }

    /**
     * @param string $testFile
     * @return int[]
     */
    public function getWarningList($testFile = '')
    {
        return [];
    }
}

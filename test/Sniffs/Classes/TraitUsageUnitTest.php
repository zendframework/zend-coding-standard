<?php
namespace ZendCodingStandardTest\Sniffs\Classes;

use ZendCodingStandardTest\Sniffs\TestCase;

class TraitUsageUnitTest extends TestCase
{
    /**
     * @param string $testFile
     * @return int[]
     */
    public function getErrorList($testFile = '')
    {
        return [
            9 => 1,
            11 => 1,
            12 => 1,
            15 => 2,
            17 => 1,
            20 => 2,
            22 => 1,
            25 => 3,
            26 => 4,
            35 => 1,
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

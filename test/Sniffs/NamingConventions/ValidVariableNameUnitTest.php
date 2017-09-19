<?php
namespace ZendCodingStandardTest\Sniffs\NamingConventions;

use ZendCodingStandardTest\Sniffs\TestCase;

class ValidVariableNameUnitTest extends TestCase
{
    /**
     * @param string $testFile
     * @return int[]
     */
    public function getErrorList($testFile = '')
    {
        return [
            15 => 1,
            16 => 1,
            28 => 1,
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

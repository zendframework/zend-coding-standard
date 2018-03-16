<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\PHP;

use ZendCodingStandardTest\Sniffs\TestCase;

class ImportInternalConstantUnitTest extends TestCase
{
    /**
     * @param string $testFile
     * @return int[]
     */
    public function getErrorList($testFile = '')
    {
        switch ($testFile) {
            case 'ImportInternalConstantUnitTest.1.inc':
                return [
                    4 => 1,
                    5 => 1,
                    11 => 1,
                    12 => 1,
                    18 => 1,
                ];
            case 'ImportInternalConstantUnitTest.2.inc':
                return [
                    5 => 1,
                    6 => 1,
                ];
            case 'ImportInternalConstantUnitTest.3.inc':
                return [
                    6 => 1,
                ];
            case 'ImportInternalConstantUnitTest.4.inc':
                return [
                    5 => 1,
                    8 => 1,
                ];
        }

        return [
            5 => 1,
            7 => 1,
            8 => 2,
            21 => 1,
            26 => 1,
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

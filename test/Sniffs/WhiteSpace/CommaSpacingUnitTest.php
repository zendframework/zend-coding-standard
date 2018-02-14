<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\WhiteSpace;

use ZendCodingStandardTest\Sniffs\TestCase;

class CommaSpacingUnitTest extends TestCase
{
    /**
     * @param string $testFile
     * @return int[]
     */
    public function getErrorList($testFile = '')
    {
        return [
            3 => 1,
            5 => 1,
            7 => 1,
            10 => 2,
            12 => 1,
            14 => 2,
            28 => 2,
            30 => 2,
            34 => 2,
            38 => 2,
            41 => 1,
            44 => 1,
            48 => 2,
            53 => 2,
            54 => 3,
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

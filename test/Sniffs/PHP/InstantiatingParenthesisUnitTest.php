<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\PHP;

use ZendCodingStandardTest\Sniffs\TestCase;

class InstantiatingParenthesisUnitTest extends TestCase
{
    /**
     * @param string $testFile
     * @return int[]
     */
    public function getErrorList($testFile = '')
    {
        return [
            3 => 1,
            4 => 1,
            7 => 1,
            14 => 1,
            18 => 1,
            22 => 1,
            23 => 1,
            24 => 1,
            27 => 1,
            29 => 1,
            30 => 1,
            32 => 1,
            33 => 1,
            35 => 1,
            37 => 1,
            40 => 1,
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

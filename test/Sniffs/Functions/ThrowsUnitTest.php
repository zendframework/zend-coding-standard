<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Functions;

use ZendCodingStandardTest\Sniffs\TestCase;

class ThrowsUnitTest extends TestCase
{
    /**
     * @param string $testFile
     * @return int[]
     */
    public function getErrorList($testFile = '')
    {
        return [
            13 => 1,
            18 => 1,
            28 => 1,
            40 => 1,
            46 => 1,
            50 => 1,
            60 => 2,
            68 => 1,
            78 => 1,
            97 => 1,
            127 => 1,
            142 => 1,
            160 => 1,
            171 => 1,
            178 => 1,
            184 => 1,
            189 => 1,
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

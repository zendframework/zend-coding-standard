<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Classes;

use ZendCodingStandardTest\Sniffs\TestCase;

class ConstVisibilityUnitTest extends TestCase
{
    /**
     * @param string $testFile
     * @return int[]
     */
    public function getErrorList($testFile = '')
    {
        return [
            6 => 1,
            15 => 1,
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

<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\PHP;

use ZendCodingStandardTest\Sniffs\TestCase;

class SingleSemicolonUnitTest extends TestCase
{
    /**
     * @param string $testFile
     * @return int[]
     */
    public function getErrorList($testFile = '')
    {
        return [
            3 => 1,
            5 => 3,
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

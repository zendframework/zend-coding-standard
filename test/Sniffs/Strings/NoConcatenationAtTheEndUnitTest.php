<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Strings;

use ZendCodingStandardTest\Sniffs\TestCase;

class NoConcatenationAtTheEndUnitTest extends TestCase
{
    /**
     * @param string $testFile
     * @return int[]
     */
    public function getErrorList($testFile = '')
    {
        return [
            3 => 1,
            9 => 1,
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

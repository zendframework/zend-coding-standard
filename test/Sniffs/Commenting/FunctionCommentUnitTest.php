<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Commenting;

use ZendCodingStandardTest\Sniffs\TestCase;

class FunctionCommentUnitTest extends TestCase
{
    /**
     * @param string $testFile
     * @return int[]
     */
    public function getErrorList($testFile = '')
    {
        return [
            7 => 1,
            11 => 1,
            17 => 1,
            29 => 1,
            35 => 1,
            36 => 1,
            39 => 1,
            42 => 1,
            49 => 1,
            57 => 1,
            72 => 1,
            79 => 1,
            85 => 1,
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

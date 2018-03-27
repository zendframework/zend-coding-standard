<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Commenting;

use ZendCodingStandardTest\Sniffs\TestCase;

class FunctionCommentUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            7 => 1,
            11 => 1,
            17 => 1,
            30 => 1,
            31 => 1,
            34 => 1,
            37 => 1,
            44 => 1,
            52 => 1,
            67 => 1,
            74 => 1,
            80 => 1,
            96 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}

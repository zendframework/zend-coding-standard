<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\PHP;

use ZendCodingStandardTest\Sniffs\TestCase;

class RedundantSemicolonUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            4 => 1,
            7 => 1,
            10 => 1,
            13 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}

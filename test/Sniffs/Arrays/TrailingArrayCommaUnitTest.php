<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Arrays;

use ZendCodingStandardTest\Sniffs\TestCase;

class TrailingArrayCommaUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            11 => 1,
            14 => 1,
            17 => 1,
            22 => 1,
            25 => 1,
            26 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}

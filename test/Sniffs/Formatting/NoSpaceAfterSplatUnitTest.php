<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Formatting;

use ZendCodingStandardTest\Sniffs\TestCase;

class NoSpaceAfterSplatUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            3 => 1,
            5 => 1,
            11 => 1,
            13 => 1,
            // 18 => 1, // we are not checking what it the next character after splat op
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}

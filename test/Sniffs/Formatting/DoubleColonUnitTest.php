<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Formatting;

use ZendCodingStandardTest\Sniffs\TestCase;

class DoubleColonUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            4 => 2,
            7 => 2,
            10 => 2,
            // 14 => 2, // double colon is preceded by and followed by comments
            18 => 2,
            24 => 2,
            31 => 2,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}

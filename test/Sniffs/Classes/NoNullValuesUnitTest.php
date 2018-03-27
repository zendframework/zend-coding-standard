<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Classes;

use ZendCodingStandardTest\Sniffs\TestCase;

class NoNullValuesUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            6 => 1,
            7 => 1,
            9 => 1,
            11 => 1,
            13 => 1,
            23 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}

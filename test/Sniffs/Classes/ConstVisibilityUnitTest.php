<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Classes;

use ZendCodingStandardTest\Sniffs\TestCase;

class ConstVisibilityUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            6 => 1,
            15 => 1,
            23 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}

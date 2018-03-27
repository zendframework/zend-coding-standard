<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Namespaces;

use ZendCodingStandardTest\Sniffs\TestCase;

class UseDoesNotStartWithBackslashUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            4 => 1,
            5 => 1,
            6 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}

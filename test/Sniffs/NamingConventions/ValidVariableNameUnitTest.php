<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\NamingConventions;

use ZendCodingStandardTest\Sniffs\TestCase;

class ValidVariableNameUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            15 => 1,
            16 => 1,
            28 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}

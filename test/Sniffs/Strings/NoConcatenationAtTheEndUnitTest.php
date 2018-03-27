<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Strings;

use ZendCodingStandardTest\Sniffs\TestCase;

class NoConcatenationAtTheEndUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            3 => 1,
            9 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}

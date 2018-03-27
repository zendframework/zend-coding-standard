<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Classes;

use ZendCodingStandardTest\Sniffs\TestCase;

class AlphabeticallySortedTraitsUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            12 => 1,
            36 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}

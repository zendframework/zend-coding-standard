<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Commenting;

use ZendCodingStandardTest\Sniffs\TestCase;

class FunctionDisallowedTagUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            12 => 1,
            13 => 1,
            14 => 1,
            15 => 1,
            16 => 1,
            17 => 1,
            19 => 1,
            20 => 1,
            30 => 1,
            31 => 1,
            32 => 1,
            33 => 1,
            35 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}

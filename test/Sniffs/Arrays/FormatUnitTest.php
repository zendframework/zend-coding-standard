<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Arrays;

use ZendCodingStandardTest\Sniffs\TestCase;

class FormatUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            2 => 2,
            14 => 2,
            16 => 2,
            19 => 1,
            20 => 1,
            22 => 1,
            25 => 1,
            31 => 3,
            33 => 1,
            38 => 1,
            39 => 1,
            47 => 2,
            49 => 1,
            53 => 2,
            55 => 1,
            56 => 1,
            62 => 2,
            63 => 1,
            68 => 1,
            69 => 1,
            74 => 1,
            75 => 1,
            76 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}

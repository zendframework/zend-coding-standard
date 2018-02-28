<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\PHP;

use ZendCodingStandardTest\Sniffs\TestCase;

class CorrectClassNameCaseUnitTest extends TestCase
{
    /**
     * @param string $testFile
     * @return int[]
     */
    public function getErrorList($testFile = '')
    {
        switch ($testFile) {
            case 'CorrectClassNameCaseUnitTest.1.inc':
                return [
                    3 => 1,
                    5 => 1,
                    8 => 1,
                    9 => 1,
                    11 => 1,
                ];
        }

        return [
            5 => 1,
            6 => 1,
            7 => 1,
            8 => 1,
            15 => 1,
            17 => 1,
            // 18 => 0,
            21 => 1,
            // 25 => 0,
            26 => 1,
            27 => 1,
            28 => 1,
            31 => 1,
            33 => 1,
            // 38 => 0,
            40 => 1,
            43 => 1,
            48 => 1,
            55 => 1,
            59 => 1,
            60 => 1,
            61 => 1,
            // 63 => 0,
            64 => 1,
            66 => 2,
            73 => 1,
            75 => 1,
            76 => 1,
            77 => 1,
            83 => 1,
            84 => 1,
            89 => 1,
        ];
    }

    /**
     * @param string $testFile
     * @return int[]
     */
    public function getWarningList($testFile = '')
    {
        return [];
    }
}

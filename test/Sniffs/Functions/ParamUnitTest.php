<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Functions;

use ZendCodingStandardTest\Sniffs\TestCase;

class ParamUnitTest extends TestCase
{
    /**
     * @param string $testFile
     * @return int[]
     */
    public function getErrorList($testFile = '')
    {
        switch ($testFile) {
            case 'ParamUnitTest.1.inc':
                return [
                    8 => 1,
                    13 => 1,
                    18 => 1,
                    23 => 1,
                    33 => 1,
                    43 => 1,
                    53 => 1,
                    63 => 1,
                    73 => 1,
                    83 => 1,
                    93 => 1,
                    98 => 1,
                    130 => 1,
                    135 => 1,
                ];
        }
        return [
            18 => 1,
            33 => 1,
            50 => 1,
            67 => 1,
            81 => 1,
            85 => 3,
            93 => 1,
            98 => 1,
            107 => 1,
            109 => 1,
            112 => 1,
            114 => 1,
            117 => 1,
            121 => 2,
            123 => 2,
            126 => 1,
            127 => 1,
            128 => 1,
            129 => 1,
            130 => 1,
            131 => 1,
            132 => 1,
            133 => 1,
            138 => 1,
            139 => 1,
            142 => 1,
            143 => 1,
            147 => 1,
            151 => 1,
            153 => 1,
            157 => 1,
            159 => 2,
            163 => 1,
            168 => 1,
            169 => 1,
            170 => 1,
            175 => 1,
            180 => 1,
            201 => 1,
            202 => 1,
            203 => 1,
            204 => 1,
            219 => 1,
            220 => 1,
            229 => 1,
            243 => 1,
            248 => 1,
            259 => 2,
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

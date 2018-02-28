<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Commenting;

use ZendCodingStandardTest\Sniffs\TestCase;

class DocCommentUnitTest extends TestCase
{
    /**
     * @param string $testFile
     * @return int[]
     */
    public function getErrorList($testFile = '')
    {
        switch ($testFile) {
            case 'DocCommentUnitTest.1.inc':
                return [
                    2 => 1,
                    8 => 1,
                    13 => 1,
                    18 => 1,
                    23 => 1,
                    26 => 1,
                    27 => 1,
                    28 => 1,
                    31 => 1,
                    38 => 1,
                    42 => 1,
                ];
            case 'DocCommentUnitTest.2.inc':
                return [
                    3 => 1,
                    9 => 1,
                    14 => 1,
                    19 => 1,
                ];
            case 'DocCommentUnitTest.3.inc':
                return [
                    1 => 1,
                    5 => 1,
                    9 => 1,
                    14 => 1,
                    18 => 1,
                ];
            case 'DocCommentUnitTest.4.inc':
                return [
                    4 => 1,
                    10 => 1,
                    14 => 1,
                    16 => 1,
                    21 => 1,
                    25 => 1,
                    30 => 1,
                ];
            case 'DocCommentUnitTest.5.inc':
                return [
                    3 => 1,
                    4 => 2,
                    9 => 1,
                    11 => 1,
                    14 => 1,
                    15 => 1,
                    16 => 1,
                    19 => 1,
                ];
            case 'DocCommentUnitTest.6.inc':
                return [
                    2 => 2,
                    8 => 1,
                    11 => 1,
                    14 => 2,
                    17 => 1,
                    20 => 2,
                ];
            case 'DocCommentUnitTest.7.inc':
                return [
                    2 => 1,
                    9 => 1,
                    13 => 1,
                ];
            case 'DocCommentUnitTest.8.inc':
                return [
                    3 => 1,
                    8 => 1,
                    12 => 1,
                    16 => 1,
                ];
            case 'DocCommentUnitTest.9.inc':
                return [
                    4 => 1,
                    10 => 1,
                    13 => 1,
                    18 => 1,
                    25 => 1,
                    27 => 1,
                    32 => 1,
                ];
        }

        return [
            3 => 1,
            8 => 1,
            10 => 1,
            11 => 1,
            12 => 1,
            13 => 1,
            16 => 1,
            17 => 1,
            25 => 1,
            26 => 1,
            29 => 1,
            32 => 1,
            35 => 1,
            36 => 1,
            37 => 1,
            42 => 1,
            45 => 1,
            48 => 1,
            53 => 2,
            55 => 1,
            59 => 1,
            62 => 1,
            65 => 1,
            70 => 1,
            73 => 1,
            75 => 1,
            79 => 1,
            80 => 1,
            81 => 1,
            84 => 1,
            89 => 1,
            90 => 1,
            94 => 1,
            96 => 1,
            99 => 1,
            103 => 1,
            104 => 1,
            110 => 1,
            111 => 1,
            127 => 1,
            133 => 1,
            138 => 1,
            142 => 2,
            143 => 2,
            145 => 2,
            146 => 2,
            150 => 2,
            158 => 2,
            161 => 1,
            162 => 1,
            164 => 1,
            170 => 1,
            179 => 1,
            180 => 1,
            183 => 1,
            184 => 1,
            185 => 1,
            192 => 1,
            203 => 1,
            214 => 1,
            215 => 1,
            216 => 1,
            217 => 1,
            218 => 1,
            223 => 1,
            229 => 1,
            236 => 1,
            241 => 1,
            246 => 1,
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

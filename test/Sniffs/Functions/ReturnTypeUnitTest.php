<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Functions;

use ZendCodingStandardTest\Sniffs\TestCase;

class ReturnTypeUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        switch ($testFile) {
            case 'ReturnTypeUnitTest.1.inc':
                return [
                    10 => 1,
                    12 => 1,
                    15 => 1,
                    18 => 1,
                    20 => 1,
                    22 => 1,
                    25 => 1,
                    28 => 1,
                    30 => 1,
                    32 => 1,
                    34 => 1,
                    37 => 1,
                    42 => 1,
                    47 => 1,
                    52 => 1,
                    57 => 1,
                    62 => 1,
                    67 => 1,
                    77 => 1,
                    82 => 1,
                    92 => 1,
                    97 => 1,
                    107 => 1,
                    112 => 1,
                    117 => 1, // in theory we can return here another class of the same parent type...
                    122 => 1,
                    127 => 1,
                    // 132 => 1, // There is no error, because return type is invalid
                    134 => 1,
                    137 => 1,
                    142 => 1,
                    147 => 1,
                    152 => 1,
                    167 => 1,
                    182 => 1,
                    197 => 1,
                    202 => 1,
                    207 => 1,
                    // 212 => 1, // There is no error, because return type is invalid
                    214 => 1,
                    217 => 1,
                    222 => 1,
                    227 => 1,
                    237 => 1,
                    242 => 1,
                    252 => 1,
                    257 => 1,
                    262 => 1,
                    267 => 1,
                    272 => 1,
                    // 277 => 1, // There is no error, because return type is invalid
                    279 => 1,
                    282 => 1,
                    287 => 2,
                    292 => 1,
                    297 => 1,
                    304 => 1,
                ];
            case 'ReturnTypeUnitTest.2.inc':
                return [
                    8 => 2,
                    13 => 1,
                    18 => 1,
                    23 => 1,
                    28 => 1,
                    33 => 2,
                    42 => 1,
                    43 => 1,
                    44 => 1,
                    45 => 1,
                    46 => 1,
                    47 => 1,
                    48 => 1,
                    49 => 1,
                    50 => 1,
                    51 => 1,
                    52 => 1,
                    53 => 1,
                    54 => 1,
                    55 => 1,
                    56 => 1,
                    57 => 1,
                    58 => 1,
                    59 => 1,
                    60 => 1,
                    61 => 1,
                    62 => 1,
                    63 => 1,
                    64 => 1,
                    65 => 1,
                    66 => 1,
                    70 => 1,
                    77 => 1,
                    84 => 1,
                    96 => 1,
                    104 => 1,
                    112 => 1,
                    116 => 1,
                    124 => 1,
                    132 => 1,
                    176 => 1,
                    184 => 1,
                    188 => 1,
                    192 => 1,
                    196 => 1,
                    200 => 1,
                    204 => 1,
                    213 => 1,
                    216 => 1,
                    265 => 1,
                    272 => 1,
                    276 => 1,
                    280 => 1,
                    284 => 1,
                    296 => 1,
                    305 => 1,
                    313 => 1,
                    318 => 1,
                    353 => 1,
                    362 => 1,
                    374 => 1,
                    383 => 1,
                    391 => 1,
                    396 => 1,
                    411 => 1,
                    419 => 1,
                    431 => 1,
                    451 => 1,
                    459 => 1,
                    467 => 1,
                ];
            case 'ReturnTypeUnitTest.3.inc':
                return [
                    8 => 1,
                    24 => 1,
                    40 => 1,
                    56 => 1,
                    72 => 1,
                    96 => 1,
                    120 => 1,
                    144 => 1,
                    160 => 1,
                ];
        }

        return [
            8 => 1,
            10 => 1,
            12 => 1,
            16 => 1,
            18 => 1,
            20 => 1,
            27 => 1,
            36 => 1,
            41 => 1,
            46 => 1,
            95 => 1,
            99 => 1,
            108 => 1,
            119 => 1,
            128 => 1,
            137 => 1,
            150 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}

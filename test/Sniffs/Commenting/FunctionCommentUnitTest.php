<?php
namespace ZendCodingStandardTest\Sniffs\Commenting;

use ZendCodingStandardTest\Sniffs\TestCase;

class FunctionCommentUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
        switch ($testFile) {
            case 'FunctionCommentUnitTest.1.inc':
                return [
                    8 => 2,
                    9 => 1,
                    17 => 2,
                    26 => 1,
                    33 => 1,
                    35 => 2,
                    49 => 1,
                    50 => 1,
                    57 => 1,
                    66 => 1,
                ];
        }

        return [
            10 => 1,
            16 => 1,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

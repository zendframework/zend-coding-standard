<?php
namespace ZendCodingStandardTest\Sniffs\Namespaces;

use ZendCodingStandardTest\Sniffs\TestCase;

class AlphabeticallySortedUsesUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
        switch ($testFile) {
            case 'AlphabeticallySortedUsesUnitTest.1.inc':
                return [
                    6 => 1,
                ];
        }

        return [
            5 => 1,
            18 => 1,
            19 => 1,
            20 => 1,
            32 => 1,
            33 => 1,
            37 => 2,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

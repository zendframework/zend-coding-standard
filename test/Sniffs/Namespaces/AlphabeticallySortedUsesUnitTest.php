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
            20 => 1,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

<?php
namespace ZendCodingStandardTest\Sniffs\Namespaces;

use ZendCodingStandardTest\Sniffs\TestCase;

class AlphabeticallySortedUsesUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
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

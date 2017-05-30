<?php
namespace ZendCodingStandardTest\Sniffs\Namespaces;

use ZendCodingStandardTest\Sniffs\TestCase;

class ConstAndFunctionKeywordsUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
        return [
            3 => 1,
            4 => 1,
            6 => 2,
            7 => 2,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

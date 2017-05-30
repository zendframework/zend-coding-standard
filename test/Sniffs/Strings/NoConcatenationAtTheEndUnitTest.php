<?php
namespace ZendCodingStandardTest\Sniffs\Strings;

use ZendCodingStandardTest\Sniffs\TestCase;

class NoConcatenationAtTheEndUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
        return [
            3 => 1,
            9 => 1,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

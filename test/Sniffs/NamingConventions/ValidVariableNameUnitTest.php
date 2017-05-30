<?php
namespace ZendCodingStandardTest\Sniffs\NamingConventions;

use ZendCodingStandardTest\Sniffs\TestCase;

class ValidVariableNameUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
        return [
            15 => 1,
            16 => 1,
            28 => 1,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

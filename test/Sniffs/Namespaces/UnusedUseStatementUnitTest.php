<?php
namespace ZendCodingStandardTest\Sniffs\Namespaces;

use ZendCodingStandardTest\Sniffs\TestCase;

class UnusedUseStatementUnitTest extends TestCase
{
    public function getErrorList()
    {
        return [];
    }

    public function getWarningList()
    {
        return [
            6 => 1,
            11 => 1,
            13 => 1,
            19 => 1,
            20 => 1,
            21 => 1,
        ];
    }
}

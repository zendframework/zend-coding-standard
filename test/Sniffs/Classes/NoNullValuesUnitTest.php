<?php
namespace ZendCodingStandardTest\Sniffs\Classes;

use ZendCodingStandardTest\Sniffs\TestCase;

class NoNullValuesUnitTest extends TestCase
{
    public function getErrorList()
    {
        return [
            6 => 1,
            7 => 1,
            9 => 1,
            11 => 1,
            13 => 1,
            // @todo: Member vars of nested class are not processed correctly
            // @see https://github.com/squizlabs/PHP_CodeSniffer/pull/1498
            // 23 => 1,
        ];
    }

    public function getWarningList()
    {
        return [];
    }
}

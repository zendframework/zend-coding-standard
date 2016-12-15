<?php
namespace ZendCodingStandard\Tests\PHP;

use ZendCodingStandard\Tests\TestCase;

class InstantiatingParenthesisUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
        return [
            3 => 1,
            4 => 1,
            7 => 1,
            14 => 1,
            18 => 1,
            22 => 1,
            23 => 1,
            24 => 1,
            27 => 1,
            29 => 1,
            30 => 1,
            32 => 1,
            33 => 1,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

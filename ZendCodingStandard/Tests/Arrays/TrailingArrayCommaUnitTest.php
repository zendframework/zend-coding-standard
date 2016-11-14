<?php
namespace ZendCodingStandard\Tests\Arrays;

use ZendCodingStandard\Tests\TestCase;

class TrailingArrayCommaUnitTest extends TestCase
{
    public function getErrorList()
    {
        return [
            11 => 1,
            14 => 1,
            17 => 1,
            22 => 1,
        ];
    }

    public function getWarningList()
    {
        return [];
    }
}

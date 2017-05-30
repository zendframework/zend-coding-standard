<?php
namespace ZendCodingStandardTest\Sniffs\Classes;

use ZendCodingStandardTest\Sniffs\TestCase;

class TraitUsageUnitTest extends TestCase
{
    public function getErrorList()
    {
        return [
            9 => 1,
            11 => 1,
            12 => 1,
            15 => 2,
            17 => 1,
            20 => 2,
            25 => 3,
            26 => 4,
            35 => 1,
        ];
    }

    public function getWarningList()
    {
        return [];
    }
}

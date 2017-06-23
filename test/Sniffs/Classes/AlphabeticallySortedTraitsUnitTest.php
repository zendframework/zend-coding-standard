<?php
namespace ZendCodingStandardTest\Sniffs\Classes;

use ZendCodingStandardTest\Sniffs\TestCase;

class AlphabeticallySortedTraitsUnitTest extends TestCase
{
    public function getErrorList()
    {
        return [
            12 => 1,
            36 => 1,
        ];
    }

    public function getWarningList()
    {
        return [];
    }
}

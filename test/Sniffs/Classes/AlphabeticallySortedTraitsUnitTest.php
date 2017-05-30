<?php
namespace ZendCodingStandardTest\Sniffs\Classes;

use ZendCodingStandardTest\Sniffs\TestCase;

class AlphabeticallySortedTraitsUnitTest extends TestCase
{
    public function getErrorList()
    {
        return [
            16 => 1,
            36 => 1,
        ];
    }

    public function getWarningList()
    {
        return [];
    }
}

<?php
namespace ZendCodingStandard\Tests\Classes;

use ZendCodingStandard\Tests\TestCase;

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

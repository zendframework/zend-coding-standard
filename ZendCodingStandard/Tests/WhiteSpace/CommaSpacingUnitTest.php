<?php
namespace ZendCodingStandard\Tests\WhiteSpace;

use ZendCodingStandard\Tests\TestCase;

class CommaSpacingUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
        return [
            3 => 1,
            5 => 1,
            7 => 1,
            10 => 2,
            12 => 1,
            14 => 2,
            23 => 2,
            25 => 2,
            29 => 2,
            33 => 2,
            36 => 1,
            39 => 1,
            43 => 2,
            48 => 2,
            49 => 3,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

<?php
namespace ZendCodingStandard\Tests\Formatting;

use ZendCodingStandard\Tests\TestCase;

class NewKeywordUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
        return [
            // 3 => 1, // not checking next character after space
            6 => 1,
            8 => 1,
            10 => 1,
            14 => 1,
            16 => 1,
            18 => 1,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

<?php
namespace ZendCodingStandard\Tests\PHP;

use ZendCodingStandard\Tests\TestCase;

class RedundantSemicolonUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
        return [
            4 => 1,
            7 => 1,
            10 => 1,
            13 => 1,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

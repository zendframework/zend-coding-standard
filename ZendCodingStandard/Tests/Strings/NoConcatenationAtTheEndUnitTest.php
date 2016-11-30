<?php
namespace ZendCodingStandard\Tests\Strings;

use ZendCodingStandard\Tests\TestCase;

class NoConcatenationAtTheEndUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
        return [
            3 => 1,
            9 => 1,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

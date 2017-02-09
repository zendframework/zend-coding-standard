<?php
namespace ZendCodingStandard\Tests\Namespaces;

use ZendCodingStandard\Tests\TestCase;

class ConstAndFunctionKeywordsUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
        return [
            3 => 1,
            4 => 1,
            6 => 2,
            7 => 2,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

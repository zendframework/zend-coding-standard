<?php
namespace ZendCodingStandard\Tests\Namespaces;

use ZendCodingStandard\Tests\TestCase;

class AlphabeticallySortedUsesUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
        return [
            5 => 1,
            20 => 1,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

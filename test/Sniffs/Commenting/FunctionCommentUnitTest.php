<?php
namespace ZendCodingStandardTest\Sniffs\Commenting;

use ZendCodingStandardTest\Sniffs\TestCase;

class FunctionCommentUnitTest extends TestCase
{
    public function getErrorList($testFile = '')
    {
        return [
            10 => 1,
            16 => 1,
        ];
    }

    public function getWarningList($testFile = '')
    {
        return [];
    }
}

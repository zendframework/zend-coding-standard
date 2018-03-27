<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Commenting;

use ZendCodingStandardTest\Sniffs\TestCase;

class NoInlineCommentAfterCurlyCloseUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            10 => 1,
            11 => 1,
            12 => 1,
            24 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}

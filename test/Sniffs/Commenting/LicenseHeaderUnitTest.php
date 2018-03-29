<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Commenting;

use ZendCodingStandardTest\Sniffs\TestCase;

class LicenseHeaderUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        switch ($testFile) {
            case 'LicenseHeaderUnitTest.1.inc':
                return [];
            case 'LicenseHeaderUnitTest.2.inc':
                return [1 => 1];
            case 'LicenseHeaderUnitTest.3.inc':
                return [3 => 2];
        }

        return [
            1 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}

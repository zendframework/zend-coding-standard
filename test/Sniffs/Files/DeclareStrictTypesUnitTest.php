<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Files;

use ZendCodingStandardTest\Sniffs\TestCase;

class DeclareStrictTypesUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        switch ($testFile) {
            case 'DeclareStrictTypesUnitTest.2.inc':
                return [3 => 1];
            case 'DeclareStrictTypesUnitTest.3.inc':
                return [7 => 1];
            case 'DeclareStrictTypesUnitTest.4.inc':
                return [7 => 1];
            case 'DeclareStrictTypesUnitTest.5.inc':
                return [8 => 1];
        }

        return [];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}

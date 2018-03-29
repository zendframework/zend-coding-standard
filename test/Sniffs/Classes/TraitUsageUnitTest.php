<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYRIGHT.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Classes;

use ZendCodingStandardTest\Sniffs\TestCase;

class TraitUsageUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            9 => 1,
            11 => 1,
            12 => 1,
            15 => 2,
            17 => 1,
            20 => 2,
            22 => 1,
            25 => 3,
            26 => 4,
            35 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}

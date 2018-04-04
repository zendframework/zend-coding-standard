<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYRIGHT.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Namespaces;

use ZendCodingStandardTest\Sniffs\TestCase;

class UnusedUseStatementUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [
            6 => 1,
            11 => 1,
            13 => 1,
            19 => 1,
            20 => 1,
            21 => 1,
        ];
    }
}

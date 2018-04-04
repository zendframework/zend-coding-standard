<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYRIGHT.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\NamingConventions;

use ZendCodingStandardTest\Sniffs\TestCase;

class ValidVariableNameUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            15 => 1,
            16 => 1,
            28 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}

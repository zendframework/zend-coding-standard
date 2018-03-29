<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYRIGHT.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\WhiteSpace;

use ZendCodingStandardTest\Sniffs\TestCase;

class BlankLineUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            2 => 1,
            6 => 1,
            10 => 1,
            11 => 1,
            18 => 1,
            26 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}

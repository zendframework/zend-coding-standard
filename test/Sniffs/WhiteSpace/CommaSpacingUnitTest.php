<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYRIGHT.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\WhiteSpace;

use ZendCodingStandardTest\Sniffs\TestCase;

class CommaSpacingUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            3 => 1,
            5 => 1,
            7 => 1,
            10 => 2,
            12 => 1,
            14 => 2,
            28 => 2,
            30 => 2,
            34 => 2,
            38 => 2,
            41 => 1,
            44 => 1,
            48 => 2,
            53 => 2,
            54 => 3,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}

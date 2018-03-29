<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYRIGHT.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendCodingStandardTest\Sniffs\Formatting;

use ZendCodingStandardTest\Sniffs\TestCase;

class NewKeywordUnitTest extends TestCase
{
    public function getErrorList(string $testFile = '') : array
    {
        return [
            // 3 => 1, // not checking next character after space
            6 => 1,
            8 => 1,
            10 => 1,
            14 => 1,
            16 => 1,
            18 => 1,
        ];
    }

    public function getWarningList(string $testFile = '') : array
    {
        return [];
    }
}

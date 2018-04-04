<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYRIGHT.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHP_CodeSniffer\Util\Common;

use function ltrim;

use const T_DOUBLE_COLON;
use const T_WHITESPACE;

class ValidVariableNameSniff extends AbstractVariableSniff
{
    /**
     * @var array
     */
    protected $phpReservedVars = [
        '_SERVER' => true,
        '_GET' => true,
        '_POST' => true,
        '_REQUEST' => true,
        '_SESSION' => true,
        '_ENV' => true,
        '_COOKIE' => true,
        '_FILES' => true,
        'GLOBALS' => true,
    ];

    /**
     * @param int $stackPtr
     */
    protected function processVariable(File $phpcsFile, $stackPtr) : void
    {
        $tokens = $phpcsFile->getTokens();
        $varName = ltrim($tokens[$stackPtr]['content'], '$');

        // If it's a php reserved var, then its ok.
        if (isset($this->phpReservedVars[$varName])) {
            return;
        }

        $objOperator = $phpcsFile->findPrevious([T_WHITESPACE], $stackPtr - 1, null, true);
        if ($tokens[$objOperator]['code'] === T_DOUBLE_COLON) {
            return; // skip MyClass::$variable, there might be no control over the declaration
        }

        if (! Common::isCamelCaps($varName, false, true, false)) {
            $error = 'Variable "%s" is not in valid camel caps format';
            $data = [$varName];
            $phpcsFile->addError($error, $stackPtr, 'NotCamelCaps', $data);
        }
    }

    /**
     * @param int $stackPtr
     */
    protected function processMemberVar(File $phpcsFile, $stackPtr) : void
    {
        // handled by PSR2.Classes.PropertyDeclaration
    }

    /**
     * @param int $stackPtr
     */
    protected function processVariableInString(File $phpcsFile, $stackPtr) : void
    {
        // handled by Squiz.Strings.DoubleQuoteUsage
    }
}

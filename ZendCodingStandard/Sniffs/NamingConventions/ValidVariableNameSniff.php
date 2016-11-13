<?php
/**
 * Copied from:
 * @see https://github.com/consistence/coding-standard/blob/master/Consistence/Sniffs/NamingConventions/ValidVariableNameSniff.php
 */

namespace ZendCodingStandard\Sniffs\NamingConventions;

use PHP_CodeSniffer;
use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Standards_AbstractVariableSniff;

class ValidVariableNameSniff extends PHP_CodeSniffer_Standards_AbstractVariableSniff
{
    const CODE_CAMEL_CAPS = 'NotCamelCaps';

    /** @var string[] */
    private $phpReservedVars = [
        '_SERVER',
        '_GET',
        '_POST',
        '_REQUEST',
        '_SESSION',
        '_ENV',
        '_COOKIE',
        '_FILES',
        'GLOBALS',
    ];

    /**
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param int $stackPtr Position of the double quoted string.
     */
    protected function processVariable(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $varName = ltrim($tokens[$stackPtr]['content'], '$');

        // If it's a php reserved var, then its ok.
        if (in_array($varName, $this->phpReservedVars, true)) {
            return;
        }

        $objOperator = $phpcsFile->findPrevious([T_WHITESPACE], ($stackPtr - 1), null, true);
        if ($tokens[$objOperator]['code'] === T_DOUBLE_COLON) {
            return; // skip MyClass::$variable, there might be no control over the declaration
        }

        if (! PHP_CodeSniffer::isCamelCaps($varName, false, true, false)) {
            $error = 'Variable "%s" is not in valid camel caps format';
            $data = [$varName];
            $phpcsFile->addError($error, $stackPtr, self::CODE_CAMEL_CAPS, $data);
        }
    }

    /**
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param int $stackPtr Position of the double quoted string.
     */
    protected function processMemberVar(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        // handled by PSR2.Classes.PropertyDeclaration
    }

    /**
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param int $stackPtr Position of the double quoted string.
     */
    protected function processVariableInString(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        // handled by Squiz.Strings.DoubleQuoteUsage
    }
}

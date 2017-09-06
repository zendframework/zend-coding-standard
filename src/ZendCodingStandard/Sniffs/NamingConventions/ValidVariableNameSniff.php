<?php
/**
 * Copied from:
 *
 * @see https://github.com/consistence/coding-standard/blob/master/Consistence/Sniffs/NamingConventions/ValidVariableNameSniff.php
 */

namespace ZendCodingStandard\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHP_CodeSniffer\Util\Common;

use function in_array;
use function ltrim;

use const T_DOUBLE_COLON;
use const T_WHITESPACE;

class ValidVariableNameSniff extends AbstractVariableSniff
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
     * @inheritDoc
     */
    protected function processVariable(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $varName = ltrim($tokens[$stackPtr]['content'], '$');

        // If it's a php reserved var, then its ok.
        if (in_array($varName, $this->phpReservedVars, true)) {
            return;
        }

        $objOperator = $phpcsFile->findPrevious([T_WHITESPACE], $stackPtr - 1, null, true);
        if ($tokens[$objOperator]['code'] === T_DOUBLE_COLON) {
            return; // skip MyClass::$variable, there might be no control over the declaration
        }

        if (! Common::isCamelCaps($varName, false, true, false)) {
            $error = 'Variable "%s" is not in valid camel caps format';
            $data = [$varName];
            $phpcsFile->addError($error, $stackPtr, self::CODE_CAMEL_CAPS, $data);
        }
    }

    /**
     * @inheritDoc
     */
    protected function processMemberVar(File $phpcsFile, $stackPtr)
    {
        // handled by PSR2.Classes.PropertyDeclaration
    }

    /**
     * @inheritDoc
     */
    protected function processVariableInString(File $phpcsFile, $stackPtr)
    {
        // handled by Squiz.Strings.DoubleQuoteUsage
    }
}

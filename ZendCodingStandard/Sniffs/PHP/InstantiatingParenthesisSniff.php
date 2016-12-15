<?php
namespace ZendCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;
use PHP_CodeSniffer_Tokens;

class InstantiatingParenthesisSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * @return array
     */
    public function register()
    {
        return [T_NEW];
    }

    /**
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $end = $phpcsFile->findNext(
            array_merge(PHP_CodeSniffer_Tokens::$emptyTokens, [
                T_NS_SEPARATOR,
                T_STRING,
                T_VARIABLE,
                T_STATIC,
            ]),
            $stackPtr + 1,
            null,
            true
        );

        if ($tokens[$end]['code'] !== T_OPEN_PARENTHESIS) {
            $error = 'Missing parenthesis on instantiating a new class.';
            $fix = $phpcsFile->addFixableError($error, $stackPtr);

            if ($fix) {
                $phpcsFile->fixer->addContentBefore($end, '()');
            }
        }
    }
}

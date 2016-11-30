<?php
namespace ZendCodingStandard\Sniffs\Strings;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;

class NoConcatenationAtTheEndSniff implements PHP_CodeSniffer_Sniff
{
    public function register()
    {
        return [T_STRING_CONCAT];
    }

    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $next = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);

        if ($tokens[$stackPtr]['line'] === $tokens[$next]['line']) {
            return;
        }

        $error = 'String concatenation character is not allowed at the end of the line.';
        $fix = $phpcsFile->addFixableError($error, $stackPtr);

        if ($fix) {
            $phpcsFile->fixer->beginChangeset();
            if ($tokens[$stackPtr - 1]['code'] === T_WHITESPACE) {
                $phpcsFile->fixer->replaceToken($stackPtr - 1, '');
            }
            $phpcsFile->fixer->replaceToken($stackPtr, '');
            $phpcsFile->fixer->addContentBefore($next, '. ');
            $phpcsFile->fixer->endChangeset();
        }
    }
}

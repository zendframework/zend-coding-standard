<?php
namespace ZendCodingStandard\Sniffs\Strings;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class NoConcatenationAtTheEndSniff implements Sniff
{
    /**
     * @inheritDoc
     */
    public function register()
    {
        return [T_STRING_CONCAT];
    }

    /**
     * @inheritDoc
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $next = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);

        if ($tokens[$stackPtr]['line'] === $tokens[$next]['line']) {
            return;
        }

        $error = 'String concatenation character is not allowed at the end of the line.';
        $fix = $phpcsFile->addFixableError($error, $stackPtr, '');

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

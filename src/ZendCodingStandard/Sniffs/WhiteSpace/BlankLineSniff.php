<?php
namespace ZendCodingStandard\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class BlankLineSniff implements Sniff
{
    /**
     * @inheritDoc
     */
    public function register()
    {
        return [
            T_COMMENT,
            T_OPEN_TAG,
            T_WHITESPACE,
        ];
    }

    /**
     * @inheritDoc
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $next = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);
        if ($next && $tokens[$stackPtr]['line'] < $tokens[$next]['line'] - 2) {
            $error = 'Unexpected blank line found.';
            $fix = $phpcsFile->addFixableError($error, $stackPtr + 1, 'BlankLine');

            if ($fix) {
                $phpcsFile->fixer->replaceToken($stackPtr + 1, '');
            }
        }
    }
}

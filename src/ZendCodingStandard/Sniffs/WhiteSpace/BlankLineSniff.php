<?php

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use const T_COMMENT;
use const T_OPEN_TAG;
use const T_WHITESPACE;

class BlankLineSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [
            T_COMMENT,
            T_OPEN_TAG,
            T_WHITESPACE,
        ];
    }

    /**
     * @param int $stackPtr
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

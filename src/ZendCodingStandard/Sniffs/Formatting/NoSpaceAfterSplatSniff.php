<?php

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use const T_ELLIPSIS;
use const T_WHITESPACE;

class NoSpaceAfterSplatSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_ELLIPSIS];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr + 1]['code'] === T_WHITESPACE) {
            $error = 'A splat operator must not be followed by a space';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceFound');

            if ($fix) {
                $phpcsFile->fixer->replaceToken($stackPtr + 1, '');
            }
        }
    }
}

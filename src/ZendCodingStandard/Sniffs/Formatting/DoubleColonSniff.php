<?php

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use const T_DOUBLE_COLON;
use const T_WHITESPACE;

class DoubleColonSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_DOUBLE_COLON];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr - 1]['code'] === T_WHITESPACE) {
            $error = 'A double colon must not be preceded by a whitespace.';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceBefore');

            if ($fix) {
                $phpcsFile->fixer->replaceToken($stackPtr - 1, '');
            }
        }

        if ($tokens[$stackPtr + 1]['code'] === T_WHITESPACE) {
            $error = 'A double colon must not be followed by a whitespace.';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceAfter');

            if ($fix) {
                $phpcsFile->fixer->replaceToken($stackPtr + 1, '');
            }
        }
    }
}

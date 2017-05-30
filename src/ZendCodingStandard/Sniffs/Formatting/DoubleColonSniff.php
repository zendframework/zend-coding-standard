<?php
namespace ZendCodingStandard\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class DoubleColonSniff implements Sniff
{
    /**
     * @return array
     */
    public function register()
    {
        return [T_DOUBLE_COLON];
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr - 1]['code'] === T_WHITESPACE) {
            $error = 'A double colon must not be preceded by a whitespace.';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, '');

            if ($fix) {
                $phpcsFile->fixer->replaceToken($stackPtr - 1, '');
            }
        }

        if ($tokens[$stackPtr + 1]['code'] === T_WHITESPACE) {
            $error = 'A double colon must not be followed by a whitespace.';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, '');

            if ($fix) {
                $phpcsFile->fixer->replaceToken($stackPtr + 1, '');
            }
        }
    }
}

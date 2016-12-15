<?php
namespace ZendCodingStandard\Sniffs\Formatting;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;

class NoSpaceAfterSplatSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * @return array
     */
    public function register()
    {
        return [T_ELLIPSIS];
    }

    /**
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
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

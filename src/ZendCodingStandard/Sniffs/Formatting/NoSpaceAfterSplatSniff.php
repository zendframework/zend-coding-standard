<?php
namespace ZendCodingStandard\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class NoSpaceAfterSplatSniff implements Sniff
{
    /**
     * @return array
     */
    public function register()
    {
        return [T_ELLIPSIS];
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return void
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

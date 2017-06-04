<?php
namespace ZendCodingStandard\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class NoSpaceAfterSplatSniff implements Sniff
{
    /**
     * @inheritDoc
     */
    public function register()
    {
        return [T_ELLIPSIS];
    }

    /**
     * @inheritDoc
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

<?php
namespace ZendCodingStandard\Sniffs\Formatting;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;

class NewKeywordSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * @return array
     */
    public function register()
    {
        return [T_NEW];
    }

    /**
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr + 1]['code'] !== T_WHITESPACE) {
            $error = 'A "new" keyword must be followed by a single space.';
            $fix = $phpcsFile->addFixableError($error, $stackPtr);

            if ($fix) {
                $phpcsFile->fixer->addContent($stackPtr, ' ');
            }
        } elseif ($tokens[$stackPtr + 1]['content'] !== ' ') {
            $error = 'A "new" keyword must be followed by a single space.';
            $fix = $phpcsFile->addFixableError($error, $stackPtr);

            if ($fix) {
                $phpcsFile->fixer->replaceToken($stackPtr + 1, ' ');
            }
        }
    }
}

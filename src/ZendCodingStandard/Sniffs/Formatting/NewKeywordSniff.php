<?php
namespace ZendCodingStandard\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use const T_NEW;
use const T_WHITESPACE;

class NewKeywordSniff implements Sniff
{
    /**
     * @inheritDoc
     */
    public function register()
    {
        return [T_NEW];
    }

    /**
     * @inheritDoc
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr + 1]['code'] !== T_WHITESPACE) {
            $error = 'A "new" keyword must be followed by a single space.';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'MissingSpace');

            if ($fix) {
                $phpcsFile->fixer->addContent($stackPtr, ' ');
            }
        } elseif ($tokens[$stackPtr + 1]['content'] !== ' ') {
            $error = 'A "new" keyword must be followed by a single space.';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'TooManySpaces');

            if ($fix) {
                $phpcsFile->fixer->replaceToken($stackPtr + 1, ' ');
            }
        }
    }
}

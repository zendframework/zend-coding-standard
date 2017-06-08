<?php
namespace ZendCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHP_CodeSniffer\Util\Tokens;

class NoNullValuesSniff extends AbstractVariableSniff
{
    /**
     * @inheritDoc
     */
    protected function processMemberVar(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $token = $tokens[$stackPtr];

        $next = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true);
        if ($tokens[$next]['code'] !== T_EQUAL) {
            return;
        }

        $value = $phpcsFile->findNext(Tokens::$emptyTokens, $next + 1, null, true);
        if ($tokens[$value]['code'] === T_NULL) {
            $error = 'Default null value for the property is redundant.';
            $fix = $phpcsFile->addFixableError($error, $value, 'NullValue');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = $stackPtr + 1; $i <= $value; ++$i) {
                    if (! in_array($tokens[$i]['code'], [T_WHITESPACE, T_EQUAL, T_NULL], true)) {
                        continue;
                    }

                    $phpcsFile->fixer->replaceToken($i, '');
                }
                $phpcsFile->fixer->endChangeset();
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function processVariable(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $token = $tokens[$stackPtr];

        $stop = 1;
    }

    /**
     * @inheritDoc
     */
    protected function processVariableInString(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $token = $tokens[$stackPtr];

        $stop = 1;
    }
}

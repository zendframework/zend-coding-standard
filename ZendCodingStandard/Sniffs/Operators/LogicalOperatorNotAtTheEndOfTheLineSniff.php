<?php
namespace ZendCodingStandard\Sniffs\Operators;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;
use PHP_CodeSniffer_Tokens;

class LogicalOperatorNotAtTheEndOfTheLineSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * @return int[]
     */
    public function register()
    {
        return [T_BOOLEAN_AND, T_BOOLEAN_OR, T_LOGICAL_AND, T_LOGICAL_OR, T_LOGICAL_XOR];
    }

    /**
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $prev = $phpcsFile->findPrevious(
            PHP_CodeSniffer_Tokens::$emptyTokens,
            $stackPtr - 1,
            null,
            true
        );
        $next = $phpcsFile->findNext(
            PHP_CodeSniffer_Tokens::$emptyTokens,
            $stackPtr + 1,
            null,
            true
        );

        if ($tokens[$prev]['line'] === $tokens[$stackPtr]['line']
            && $tokens[$next]['line'] !== $tokens[$stackPtr]['line']
        ) {
            $fix = $phpcsFile->addFixableError('Logical operator cannot be at the end of the line.', $stackPtr);

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->replaceToken($stackPtr, '');
                for ($i = $stackPtr - 1; $i > $prev; $i--) {
                    if ($tokens[$i]['code'] !== T_WHITESPACE) {
                        break;
                    }
                    $phpcsFile->fixer->replaceToken($i, '');
                }
                $phpcsFile->fixer->addContentBefore($next, $tokens[$stackPtr]['content'] . ' ');
                $phpcsFile->fixer->endChangeset();
            }
        }
    }
}

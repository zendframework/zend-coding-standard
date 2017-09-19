<?php
namespace ZendCodingStandard\Sniffs\Operators;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use const T_WHITESPACE;

class BooleanOperatorSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register()
    {
        return Tokens::$booleanOperators;
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $prev = $phpcsFile->findPrevious(
            Tokens::$emptyTokens,
            $stackPtr - 1,
            null,
            true
        );
        $next = $phpcsFile->findNext(
            Tokens::$emptyTokens,
            $stackPtr + 1,
            null,
            true
        );

        if ($tokens[$prev]['line'] === $tokens[$stackPtr]['line']
            && $tokens[$next]['line'] !== $tokens[$stackPtr]['line']
        ) {
            $error = 'Logical operator cannot be at the end of the line.';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'OperatorAtTheEnd');

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

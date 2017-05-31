<?php
namespace ZendCodingStandard\Sniffs\Methods;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ReturnValueWithoutBracketsSniff implements Sniff
{
    public function register()
    {
        return [T_RETURN];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $firstContent = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);
        // If the first non-whitespace token is not an opening parenthesis, then we are not concerned.
        if ($tokens[$firstContent]['code'] !== T_OPEN_PARENTHESIS) {
            $phpcsFile->recordMetric($stackPtr, 'Brackets around returned value', 'no');
            return;
        }

        // Find the end of the expression.
        $end = $stackPtr;
        do {
            $end = $phpcsFile->findNext([T_SEMICOLON, T_CLOSE_TAG], $end + 1, null, false);
        } while ($tokens[$end]['level'] !== $tokens[$stackPtr]['level']);

        // If the token before the semi-colon is not a closing parenthesis, then we are not concerned.
        $prev = $phpcsFile->findPrevious(T_WHITESPACE, $end - 1, null, true);
        if ($tokens[$prev]['code'] !== T_CLOSE_PARENTHESIS) {
            $phpcsFile->recordMetric($stackPtr, 'Brackets around returned value', 'no');
            return;
        }

        // If the parenthesis don't match, then we are not concerned.
        if ($tokens[$firstContent]['parenthesis_closer'] !== $prev) {
            $phpcsFile->recordMetric($stackPtr, 'Brackets around returned value', 'no');
            return;
        }

        $phpcsFile->recordMetric($stackPtr, 'Brackets around returned value', 'yes');

        $error = 'Returned value should not be bracketed';
        $fix = $phpcsFile->addFixableError($error, $stackPtr, 'HasBracket');
        if ($fix === true) {
            $phpcsFile->fixer->beginChangeset();
            $phpcsFile->fixer->replaceToken($firstContent, '');
            if ($tokens[$firstContent - 1]['code'] !== T_WHITESPACE) {
                $phpcsFile->fixer->addContent($firstContent - 1, ' ');
            }
            $phpcsFile->fixer->replaceToken($prev, '');
            $phpcsFile->fixer->endChangeset();
        }
    }
}

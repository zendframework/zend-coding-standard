<?php

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractArraySniff;
use PHP_CodeSniffer\Util\Tokens;

use const T_COMMA;

class TrailingArrayCommaSniff extends AbstractArraySniff
{
    /**
     * Processes a single-line array definition.
     *
     * @param File $phpcsFile The current file being checked.
     * @param int $stackPtr The position of the current token
     *     in the stack passed in $tokens.
     * @param int $arrayStart The token that starts the array definition.
     * @param int $arrayEnd The token that ends the array definition.
     * @param array $indices An array of token positions for the array keys,
     *     double arrows, and values.
     */
    protected function processSingleLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $indices) : void
    {
        $tokens = $phpcsFile->getTokens();
        $beforeClose = $phpcsFile->findPrevious(Tokens::$emptyTokens, $arrayEnd - 1, $arrayStart + 1, true);

        if ($beforeClose && $tokens[$beforeClose]['code'] === T_COMMA) {
            $error = 'Single-line arrays must not have a trailing comma after the last element';
            $fix = $phpcsFile->addFixableError($error, $beforeClose, 'AdditionalTrailingComma');

            if ($fix) {
                $phpcsFile->fixer->replaceToken($beforeClose, '');
            }
        }
    }

    /**
     * Processes a multi-line array definition.
     *
     * @param File $phpcsFile The current file being checked.
     * @param int $stackPtr The position of the current token
     *     in the stack passed in $tokens.
     * @param int $arrayStart The token that starts the array definition.
     * @param int $arrayEnd The token that ends the array definition.
     * @param array $indices An array of token positions for the array keys,
     *     double arrows, and values.
     */
    protected function processMultiLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $indices) : void
    {
        $tokens = $phpcsFile->getTokens();
        $beforeClose = $phpcsFile->findPrevious(Tokens::$emptyTokens, $arrayEnd - 1, $arrayStart + 1, true);

        if ($beforeClose && $tokens[$beforeClose]['code'] !== T_COMMA) {
            $error = 'Multi-line arrays must have a trailing comma after the last element';
            $fix = $phpcsFile->addFixableError($error, $beforeClose, 'MissingTrailingComma');

            if ($fix) {
                $phpcsFile->fixer->addContent($beforeClose, ',');
            }
        }
    }
}

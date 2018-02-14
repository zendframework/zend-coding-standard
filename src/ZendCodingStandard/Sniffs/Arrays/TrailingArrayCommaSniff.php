<?php
/**
 * Copied from SlevomatCodingStandard\Sniffs\Arrays\TrailingArrayCommaSniff
 * with fix: comma shouldn't be added after "[" for empty arrays "[]".
 */

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use const T_COMMA;
use const T_COMMENT;
use const T_DOC_COMMENT;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_DOC_COMMENT_STAR;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_TAG;
use const T_DOC_COMMENT_WHITESPACE;
use const T_OPEN_SHORT_ARRAY;
use const T_WHITESPACE;

class TrailingArrayCommaSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_OPEN_SHORT_ARRAY];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $arrayToken = $tokens[$stackPtr];
        $closeParenthesisPointer = $arrayToken['bracket_closer'];
        $openParenthesisToken = $tokens[$arrayToken['bracket_opener']];
        $closeParenthesisToken = $tokens[$closeParenthesisPointer];

        if ($openParenthesisToken['line'] === $closeParenthesisToken['line']) {
            return;
        }

        $previousToCloseParenthesisPointer = $phpcsFile->findPrevious(
            [
                T_WHITESPACE,
                T_COMMENT,
                T_DOC_COMMENT,
                T_DOC_COMMENT_OPEN_TAG,
                T_DOC_COMMENT_CLOSE_TAG,
                T_DOC_COMMENT_STAR,
                T_DOC_COMMENT_STRING,
                T_DOC_COMMENT_TAG,
                T_DOC_COMMENT_WHITESPACE,
            ],
            $closeParenthesisPointer - 1,
            null,
            true
        );

        $previousToCloseParenthesisToken = $tokens[$previousToCloseParenthesisPointer];

        if ($previousToCloseParenthesisToken['code'] !== T_COMMA
            && $previousToCloseParenthesisToken['code'] !== T_OPEN_SHORT_ARRAY
            && $closeParenthesisToken['line'] !== $previousToCloseParenthesisToken['line']
        ) {
            $error = 'Multiline arrays must have a trailing comma after the last element';
            $fix = $phpcsFile->addFixableError($error, $previousToCloseParenthesisPointer, 'TrailingComma');

            if ($fix) {
                $phpcsFile->fixer->addContent($previousToCloseParenthesisPointer, ',');
            }
        }
    }
}

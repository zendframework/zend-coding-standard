<?php
/**
 * Copied from SlevomatCodingStandard\Sniffs\Arrays\TrailingArrayCommaSniff
 * with fix: comma shouldn't be added after "[" for empty arrays "[]".
 */
namespace ZendCodingStandard\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class TrailingArrayCommaSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register()
    {
        return [T_OPEN_SHORT_ARRAY];
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return void
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
            $fix = $phpcsFile->addFixableError(
                'Multiline arrays must have a trailing comma after the last element',
                $previousToCloseParenthesisPointer,
                ''
            );

            if ($fix) {
                $phpcsFile->fixer->addContent($previousToCloseParenthesisPointer, ',');
            }
        }
    }
}

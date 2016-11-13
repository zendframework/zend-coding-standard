<?php
/**
 * Copied from SlevomatCodingStandard\Sniffs\Arrays\TrailingArrayCommaSniff
 * with fix: comma shouldn't be added after "[" for empty arrays "[]".
 */
namespace ZendCodingStandard\Sniffs\Arrays;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;

class TrailingArrayCommaSniff implements PHP_CodeSniffer_Sniff
{
    public function register()
    {
        return [T_OPEN_SHORT_ARRAY];
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the current token in
     *                      the stack passed in $tokens.
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
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
                $previousToCloseParenthesisPointer
            );

            if ($fix) {
                $phpcsFile->fixer->addContent($previousToCloseParenthesisPointer, ',');
            }
        }
    }
}

<?php
/**
 * Check spaces before and after comma.
 * Before comma space is not allowed.
 * After comma should be exactly one comma.
 * There is allowed more than one space after comma only in when this is multidimensional array.
 *
 * @todo: maybe we need fix part for multidimensional array, now it's checking for something like:
 * [
 *   [1,    3423, 342, 4324],
 *   [4432, 43,   4,   32],
 *   [22,   3432, 23,  4],
 * ]
 */

namespace ZendCodingStandard\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class CommaSpacingSniff implements Sniff
{
    /**
     * @inheritDoc
     */
    public function register()
    {
        return [T_COMMA];
    }

    /**
     * @inheritDoc
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Check spaces before comma.
        $prevToken = $tokens[$stackPtr - 1];
        if ($prevToken['code'] === T_WHITESPACE) {
            $error = 'Expected 0 spaces before comma; found %d';
            $data = [strlen($prevToken['content'])];
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceBeforeComma', $data);
            if ($fix) {
                $phpcsFile->fixer->replaceToken($stackPtr - 1, '');
            }
        }

        $nextToken = $tokens[$stackPtr + 1];
        if ($nextToken['code'] !== T_WHITESPACE) {
            // There is no space after comma.

            $error = 'Expected 1 space after comma; found 0';
            $fix = $phpcsFile->addFixableError($error, $stackPtr + 1, 'NoSpaceAfterComma');
            if ($fix) {
                $phpcsFile->fixer->addContent($stackPtr, ' ');
            }
        } elseif ($nextToken['content'] !== ' ') {
            // There is more than one space after comma.

            $nonSpace = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);
            if ($tokens[$nonSpace]['line'] !== $tokens[$stackPtr]['line']) {
                // Next non-space token is in new line, return.

                return;
            }

            // Check if this is multidimensional array.
            $openArray = $phpcsFile->findPrevious([T_OPEN_SHORT_ARRAY], $stackPtr);
            $beforeOpening = $phpcsFile->findPrevious(
                Tokens::$emptyTokens,
                $openArray - 1,
                null,
                true
            );
            $closeArray = $phpcsFile->findNext([T_CLOSE_SHORT_ARRAY], $stackPtr);
            $afterClosing = $phpcsFile->findNext(
                array_merge(Tokens::$emptyTokens, [T_COMMA]),
                $closeArray + 1,
                null,
                true
            );

            if ($tokens[$openArray]['line'] !== $tokens[$closeArray]['line']
                || ($tokens[$beforeOpening]['line'] === $tokens[$openArray]['line']
                    && $tokens[$beforeOpening]['code'] !== T_DOUBLE_ARROW)
                || $tokens[$afterClosing]['line'] === $tokens[$closeArray]['line']
            ) {
                $error = 'Expected 1 space after comma; found %d';
                $data = [
                    strlen($nextToken['content']),
                ];
                $fix = $phpcsFile->addFixableError($error, $stackPtr + 1, 'SpacingAfterComma', $data);

                if ($fix) {
                    $phpcsFile->fixer->replaceToken($stackPtr + 1, ' ');
                }
            }
        }
    }
}

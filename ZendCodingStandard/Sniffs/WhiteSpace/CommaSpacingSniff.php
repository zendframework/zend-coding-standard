<?php
/**
 * Check spaces before and after comma.
 * Before comma space is not allowed.
 * After comma should be exactly only one comma.
 * There is allowed more than one space after comma only in when this is multidimensional array.
 *
 * @todo: maybe we need fix part for multidimensional array, no it's checking for something like:
 * [
 *   [1,    3423, 342, 4324],
 *   [4432, 43,   4,   32],
 *   [22,   3432, 23,  4],
 * ]
 */
namespace ZendCodingStandard\Sniffs\WhiteSpace;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;
use PHP_CodeSniffer_Tokens;

class CommaSpacingSniff implements PHP_CodeSniffer_Sniff
{
    public function register()
    {
        return [T_COMMA];
    }

    /**
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the current token
     *                      in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $prevToken = $tokens[$stackPtr - 1];
        $prevType = $prevToken['code'];
        if (isset(PHP_CodeSniffer_Tokens::$emptyTokens[$prevType])) {
            $nonSpace = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, $stackPtr - 2, null, true);
            $expected = $tokens[$nonSpace]['content'] . ',';
            $found = $phpcsFile->getTokensAsString($nonSpace, ($stackPtr - $nonSpace)) . ',';
            $error = 'Space found before comma; expected "%s" but found "%s"';
            $data = [
                $expected,
                $found,
            ];

            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceBeforeComma', $data);
            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = $stackPtr - 1; $i > $nonSpace; $i--) {
                    $phpcsFile->fixer->replaceToken($i, '');
                }

                $phpcsFile->fixer->endChangeset();
            }
        }

        $nextToken = $tokens[$stackPtr + 1];
        $nextType = $nextToken['code'];

        // There is no space after comma.
        if ($nextType !== T_WHITESPACE) {
            $error = 'Expected 1 space after comma; found 0';
            $fix = $phpcsFile->addFixableError($error, $stackPtr + 1, 'NoSpaceAfterComma');
            if ($fix) {
                $phpcsFile->fixer->addContent($stackPtr, ' ');
            }
        } elseif (strlen($nextToken['content']) !== 1) {
            // There is more than one space after comma.

            // Check if this is not before a comment at the end of the line
            if ($tokens[$stackPtr + 2]['code'] !== T_COMMENT
                && $tokens[$stackPtr + 3]['code'] !== T_WHITESPACE
                && $tokens[$stackPtr + 3]['content'] !== "\n"
            ) {
                if ($tokens[$stackPtr + 2]['code'] === T_DOC_COMMENT_OPEN_TAG) {
                    $phpcsFile->addError(
                        'Doc comment is not allowed here. Please use normal comment: /* ... */ or // ...',
                        $stackPtr + 2,
                        'DocCommentNotAllowed'
                    );
                } else {
                    $openArray = $phpcsFile->findPrevious([T_OPEN_SHORT_ARRAY], $stackPtr);
                    $beforeOpening = $phpcsFile->findPrevious(
                        PHP_CodeSniffer_Tokens::$emptyTokens,
                        $openArray - 1,
                        null,
                        true
                    );
                    $closeArray = $phpcsFile->findNext([T_CLOSE_SHORT_ARRAY], $stackPtr);
                    $afterClosing = $phpcsFile->findNext(
                        array_merge(PHP_CodeSniffer_Tokens::$emptyTokens, [T_COMMA]),
                        $closeArray + 1,
                        null,
                        true
                    );

                    if ($tokens[$openArray]['line'] !== $tokens[$closeArray]['line']
                        || $tokens[$beforeOpening]['line'] === $tokens[$openArray]['line']
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
    }
}

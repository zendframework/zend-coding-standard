<?php

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractArraySniff;

use function str_repeat;
use function strlen;
use function strpos;

use const T_CLOSE_CURLY_BRACKET;
use const T_CLOSE_SHORT_ARRAY;
use const T_COMMENT;
use const T_DOUBLE_ARROW;
use const T_WHITESPACE;

class FormatSniff extends AbstractArraySniff
{
    /**
     * The number of spaces code should be indented.
     *
     * @var int
     */
    public $indent = 4;

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

        // Single-line array - spaces before first element
        if ($tokens[$arrayStart + 1]['code'] === T_WHITESPACE) {
            $error = 'Expected 0 spaces after array bracket opener; %d found';
            $data = [strlen($tokens[$arrayStart + 1]['content'])];
            $fix = $phpcsFile->addFixableError($error, $arrayStart + 1, 'SingleLineSpaceBefore', $data);

            if ($fix) {
                $phpcsFile->fixer->replaceToken($arrayStart + 1, '');
            }
        }

        // Single-line array - spaces before last element
        if ($tokens[$arrayEnd - 1]['code'] === T_WHITESPACE) {
            $error = 'Expected 0 spaces before array bracket closer; %d found';
            $data = [strlen($tokens[$arrayEnd - 1]['content'])];
            $fix = $phpcsFile->addFixableError($error, $arrayEnd - 1, 'SingleLineSpaceAfter', $data);

            if ($fix) {
                $phpcsFile->fixer->replaceToken($arrayEnd - 1, '');
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

        $firstContent = $phpcsFile->findNext(T_WHITESPACE, $arrayStart + 1, null, true);
        if ($tokens[$firstContent]['code'] === T_CLOSE_SHORT_ARRAY) {
            $error = 'Empty array must be in one line';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'EmptyArrayInOneLine');

            if ($fix) {
                $phpcsFile->fixer->replaceToken($arrayStart + 1, '');
            }

            return;
        }

        $lastContent = $phpcsFile->findPrevious(T_WHITESPACE, $arrayEnd - 1, null, true);
        if ($tokens[$arrayEnd]['line'] > $tokens[$lastContent]['line'] + 1) {
            $error = 'Blank line found at the end of the array';
            $fix = $phpcsFile->addFixableError($error, $arrayEnd - 1, 'BlankLineAtTheEnd');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $i = $lastContent + 1;
                while ($tokens[$i]['line'] !== $tokens[$arrayEnd]['line']) {
                    $phpcsFile->fixer->replaceToken($i, '');
                    ++$i;
                }
                $phpcsFile->fixer->addNewlineBefore($arrayEnd);
                $phpcsFile->fixer->endChangeset();
            }
        }

        $first = $phpcsFile->findFirstOnLine([], $arrayStart, true);
        $indent = $tokens[$first]['code'] === T_WHITESPACE
            ? strlen($tokens[$first]['content'])
            : 0;

        $previousLine = $tokens[$arrayStart]['line'];
        $next = $arrayStart;
        while ($next = $phpcsFile->findNext(T_WHITESPACE, $next + 1, $arrayEnd, true)) {
            if ($previousLine === $tokens[$next]['line']) {
                if ($tokens[$next]['code'] !== T_COMMENT) {
                    $error = 'There must be one array element per line';
                    $fix = $phpcsFile->addFixableError($error, $next, 'OneElementPerLine');

                    if ($fix) {
                        $phpcsFile->fixer->beginChangeset();
                        if ($tokens[$next - 1]['code'] === T_WHITESPACE) {
                            $phpcsFile->fixer->replaceToken($next - 1, '');
                        }
                        $phpcsFile->fixer->addNewlineBefore($next);
                        $phpcsFile->fixer->endChangeset();
                    }
                }
            } else {
                if ($previousLine < $tokens[$next]['line'] - 1
                    && (! empty($tokens[$stackPtr]['conditions'])
                        || $previousLine === $tokens[$arrayStart]['line'])
                ) {
                    $firstOnLine = $phpcsFile->findFirstOnLine([], $next, true);

                    $error = 'Blank line is not allowed here';
                    $fix = $phpcsFile->addFixableError($error, $firstOnLine - 1, 'BlankLine');

                    if ($fix) {
                        $phpcsFile->fixer->replaceToken($firstOnLine - 1, '');
                    }
                }
            }

            if ($tokens[$next]['code'] === T_COMMENT
                && (strpos($tokens[$next]['content'], '//') === 0
                    || strpos($tokens[$next]['content'], '#') === 0)
            ) {
                $end = $next;
            } else {
                $end = $phpcsFile->findEndOfStatement($next);
                if ($tokens[$end]['code'] === T_DOUBLE_ARROW
                    || $tokens[$end]['code'] === T_CLOSE_CURLY_BRACKET
                ) {
                    $end = $phpcsFile->findEndOfStatement($end);
                }
            }

            $previousLine = $tokens[$end]['line'];
            $next = $end;
        }

        if ($first = $phpcsFile->findFirstOnLine([], $arrayEnd, true)) {
            if ($first < $arrayEnd - 1) {
                $error = 'Array closing bracket should be in new line';
                $fix = $phpcsFile->addFixableError($error, $arrayEnd, 'ClosingBracketInNewLine');

                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();
                    if ($indent > 0) {
                        $phpcsFile->fixer->addContentBefore($arrayEnd, str_repeat(' ', $indent));
                    }
                    $phpcsFile->fixer->addNewlineBefore($arrayEnd);
                    $phpcsFile->fixer->endChangeset();
                }
            }
        }
    }
}

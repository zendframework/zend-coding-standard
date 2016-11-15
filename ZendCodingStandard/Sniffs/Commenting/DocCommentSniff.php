<?php
/**
 * Check indents of DocComments.
 * Maybe it could do more (check also params?)
 */
namespace ZendCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;

class DocCommentSniff implements PHP_CodeSniffer_Sniff
{
    public function register()
    {
        return [T_DOC_COMMENT_OPEN_TAG];
    }

    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $commentStart = $stackPtr;
        $commentEnd = $tokens[$stackPtr]['comment_closer'];

        if ($tokens[$commentStart]['line'] == $tokens[$commentEnd]['line']) {
            return;
        }

        $short = $phpcsFile->findNext(
            [
                T_DOC_COMMENT_WHITESPACE,
                T_DOC_COMMENT_STAR,
            ],
            $stackPtr + 1,
            $commentEnd,
            true
        );
        if ($short === false) {
            // No content at all.
            $error = 'Doc comment is empty.';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Empty');
            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = $commentStart; $i <= $commentEnd; $i++) {
                    $phpcsFile->fixer->replaceToken($i, '');
                }
                $phpcsFile->fixer->endChangeset();
            }

            return;
        }

        if ($tokens[$commentEnd + 1]['content'] !== $phpcsFile->eolChar) {
            $error = 'The close comment tag must be the only content on the line.';
            $fix = $phpcsFile->addFixableError($error, $commentEnd);

            if ($fix) {
                $phpcsFile->fixer->addNewline($commentEnd);
            }

            return;
        }

        $prev = $phpcsFile->findPrevious([T_DOC_COMMENT_WHITESPACE], $commentEnd - 1, null, true);
        if ($tokens[$prev]['content'] !== $phpcsFile->eolChar
            && $tokens[$prev]['line'] === $tokens[$commentEnd]['line']
        ) {
            $error = 'The close comment tag must be the only content on the line.';
            $fix = $phpcsFile->addFixableError($error, $commentEnd);

            if ($fix) {
                $phpcsFile->fixer->addNewlineBefore($commentEnd);
            }
        }

        if ($tokens[$commentEnd + 2]['content'] === $phpcsFile->eolChar) {
            // There is empty line after doc block.
            $before = $tokens[$commentStart - 1];

            $indent = $before['code'] == T_OPEN_TAG || $before['content'] == $phpcsFile->eolChar
                ? 0
                : strlen($before['content']);
        } elseif ($tokens[$commentEnd + 2]['code'] == T_WHITESPACE) {
            $indent = strlen($tokens[$commentEnd + 2]['content']);
        } else {
            $indent = 0;
        }

        // First line of the doc comment.
        $spaces = $tokens[$commentStart - 1];
        if ($spaces['code'] === T_WHITESPACE
            && $spaces['content'] !== $phpcsFile->eolChar
            && strlen($spaces['content']) !== $indent
        ) {
            $error = 'Invalid doc comment indent. Expected %d spaces; %d found';
            $data = [
                $indent,
                strlen($spaces['content']),
            ];
            $fix = $phpcsFile->addFixableError($error, $commentStart, null, $data);

            if ($fix) {
                $phpcsFile->fixer->replaceToken($commentStart - 1, str_repeat(' ', $indent));
            }
        } elseif ($spaces['code'] === T_WHITESPACE
            && $spaces['content'] === $phpcsFile->eolChar
            && $indent > 0
        ) {
            $error = 'Invalid doc comment indent. Expected %d spaces; %d found';
            $data = [
                $indent,
                0,
            ];
            $fix = $phpcsFile->addFixableError($error, $commentStart, null, $data);

            if ($fix) {
                $phpcsFile->fixer->replaceToken($commentStart - 1, $phpcsFile->eolChar . str_repeat(' ', $indent));
            }
        }

        // Rest of the doc comment.
        $from = $commentStart;
        $search = [T_DOC_COMMENT_STAR, T_DOC_COMMENT_CLOSE_TAG];
        while ($next = $phpcsFile->findNext($search, $from + 1, $commentEnd + 1)) {
            $spaces = $tokens[$next - 1];

            if ($spaces['content'] === $phpcsFile->eolChar) {
                $error = 'Invalid doc comment indent. Expected %d spaces; %d found';
                $data = [
                    $indent + 1,
                    0,
                ];
                $fix = $phpcsFile->addFixableError($error, $next, null, $data);

                if ($fix) {
                    $phpcsFile->fixer->replaceToken($next - 1, $phpcsFile->eolChar . ' ');
                }
            } elseif ($spaces['code'] == T_DOC_COMMENT_WHITESPACE
                && strlen($spaces['content']) !== $indent + 1
            ) {
                $error = 'Invalid doc comment indent. Expected %d spaces; %d found';
                $data = [
                    $indent + 1,
                    strlen($spaces['content']),
                ];
                $fix = $phpcsFile->addFixableError($error, $next, null, $data);

                if ($fix) {
                    $phpcsFile->fixer->replaceToken($next - 1, str_repeat(' ', $indent + 1));
                }
            }

            $from = $next;
        }
    }
}

<?php
namespace ZendCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use function in_array;
use function preg_match;
use function str_repeat;
use function strlen;
use function strpos;
use function trim;

use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_DOC_COMMENT_STAR;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_TAG;
use const T_DOC_COMMENT_WHITESPACE;
use const T_NAMESPACE;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_TAG;
use const T_USE;
use const T_WHITESPACE;

class DocCommentSniff implements Sniff
{
    /**
     * @inheritDoc
     */
    public function register()
    {
        return [T_DOC_COMMENT_OPEN_TAG];
    }

    /**
     * @inheritDoc
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $commentStart = $stackPtr;
        $commentEnd = $tokens[$stackPtr]['comment_closer'];

        if ($this->checkIfEmpty($phpcsFile, $commentStart, $commentEnd)) {
            return;
        }

        $this->checkBeforeOpen($phpcsFile, $commentStart);
        $this->checkAfterClose($phpcsFile, $commentStart, $commentEnd);
        $this->checkCommentIndents($phpcsFile, $commentStart, $commentEnd);

        // Doc block comment in one line.
        if ($tokens[$commentStart]['line'] == $tokens[$commentEnd]['line']) {
            $this->checkSpacesInOneLineComment($phpcsFile, $commentStart, $commentEnd);

            return;
        }

        $this->checkAfterOpen($phpcsFile, $commentStart);
        $this->checkBeforeClose($phpcsFile, $commentEnd);

        $this->checkSpacesAfterStar($phpcsFile, $commentStart, $commentEnd);
        $this->checkBlankLinesInComment($phpcsFile, $commentStart, $commentEnd);

        $this->checkBlankLineBeforeTags($phpcsFile, $commentStart);
    }

    /**
     * Checks if doc comment is empty.
     *
     * @param File $phpcsFile
     * @param int $commentStart
     * @param int $commentEnd
     * @return bool
     */
    private function checkIfEmpty(File $phpcsFile, $commentStart, $commentEnd)
    {
        $tokens = $phpcsFile->getTokens();

        $empty = [
            T_DOC_COMMENT_WHITESPACE,
            T_DOC_COMMENT_STAR,
        ];

        $next = $commentStart;
        while ($next = $phpcsFile->findNext($empty, $next + 1, $commentEnd, true)) {
            if ($tokens[$next]['code'] === T_DOC_COMMENT_STRING
                && preg_match('/^[*\s]+$/', $tokens[$next]['content'])
            ) {
                continue;
            }

            return false;
        }

        $error = 'Doc comment is empty.';
        $fix = $phpcsFile->addFixableError($error, $commentStart, 'Empty');

        if ($fix) {
            $phpcsFile->fixer->beginChangeset();
            for ($i = $commentStart; $i <= $commentEnd; $i++) {
                $phpcsFile->fixer->replaceToken($i, '');
            }
            if ($tokens[$commentStart - 1]['code'] === T_WHITESPACE
                && strpos($tokens[$commentStart - 1]['content'], $phpcsFile->eolChar) === false
            ) {
                $phpcsFile->fixer->replaceToken($commentStart - 1, '');
                if ($tokens[$commentStart - 2]['code'] === T_WHITESPACE
                    && strpos($tokens[$commentStart - 2]['content'], $phpcsFile->eolChar) !== false
                    && $tokens[$commentEnd + 1]['code'] === T_WHITESPACE
                    && strpos($tokens[$commentEnd + 1]['content'], $phpcsFile->eolChar) !== false
                ) {
                    $phpcsFile->fixer->replaceToken($commentStart - 2, '');
                }
            } elseif ($tokens[$commentStart - 1]['code'] === T_WHITESPACE
                && strpos($tokens[$commentStart - 1]['content'], $phpcsFile->eolChar) !== false
                && $tokens[$commentEnd + 1]['code'] === T_WHITESPACE
                && strpos($tokens[$commentEnd + 1]['content'], $phpcsFile->eolChar) !== false
            ) {
                $phpcsFile->fixer->replaceToken($commentStart - 1, '');
            } elseif ($tokens[$commentStart - 1]['code'] === T_OPEN_TAG
                && ($next = $phpcsFile->findNext(T_WHITESPACE, $commentEnd + 1, null, true))
                && $tokens[$next]['line'] > $tokens[$commentEnd]['line'] + 1
            ) {
                $phpcsFile->fixer->replaceToken($commentEnd + 1, '');
            }
            $phpcsFile->fixer->endChangeset();
        }

        return true;
    }

    /**
     * Checks if there is no any other content before doc comment opening tag,
     * and if there is blank line before doc comment (for multiline doc comment).
     *
     * @param File $phpcsFile
     * @param int $commentStart
     * @return void
     */
    private function checkBeforeOpen(File $phpcsFile, $commentStart)
    {
        $tokens = $phpcsFile->getTokens();

        $previous = $phpcsFile->findPrevious(T_WHITESPACE, $commentStart - 1, null, true);
        if ($tokens[$previous]['line'] === $tokens[$commentStart]['line']) {
            $error = 'The open comment tag must be the only content on the line.';
            $fix = $phpcsFile->addFixableError($error, $commentStart, 'ContentBeforeOpeningTag');

            if ($fix) {
                $nonEmpty = $phpcsFile->findPrevious(T_WHITESPACE, $commentStart - 1, null, true);
                $phpcsFile->fixer->beginChangeset();
                $prev = $commentStart;
                while ($prev = $phpcsFile->findPrevious(T_WHITESPACE, $prev - 1, $nonEmpty)) {
                    $phpcsFile->fixer->replaceToken($prev, '');
                }
                $phpcsFile->fixer->replaceToken($nonEmpty, trim($tokens[$nonEmpty]['content']));
                $phpcsFile->fixer->addNewline($commentStart - 1);
                $phpcsFile->fixer->endChangeset();
            }
        } elseif ($tokens[$previous]['line'] === $tokens[$commentStart]['line'] - 1
            && $tokens[$previous]['code'] !== T_OPEN_TAG
            && $tokens[$previous]['code'] !== T_OPEN_CURLY_BRACKET
        ) {
            $error = 'Missing blank line before doc comment.';
            $fix = $phpcsFile->addFixableError($error, $commentStart, 'MissingBlankLine');

            if ($fix) {
                $phpcsFile->fixer->addNewlineBefore($commentStart);
            }
        }
    }

    /**
     * Checks if there is no any other content after doc comment opening tag (for multiline doc comment).
     *
     * @param File $phpcsFile
     * @param int $commentStart
     * @return void
     */
    private function checkAfterOpen(File $phpcsFile, $commentStart)
    {
        $tokens = $phpcsFile->getTokens();

        $next = $phpcsFile->findNext(T_DOC_COMMENT_WHITESPACE, $commentStart + 1, null, true);
        if ($tokens[$next]['line'] === $tokens[$commentStart]['line']) {
            $error = 'The open comment tag must be the only content on the line.';
            $fix = $phpcsFile->addFixableError($error, $commentStart, 'ContentAfterOpeningTag');

            if ($fix) {
                $indentToken = $tokens[$commentStart - 1];
                if ($indentToken['code'] === T_WHITESPACE
                    && $indentToken['line'] === $tokens[$commentStart]['line']
                ) {
                    $indent = strlen($indentToken['content']);
                } else {
                    $indent = 0;
                }

                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->addNewline($commentStart);
                if ($tokens[$commentStart + 1]['code'] === T_DOC_COMMENT_WHITESPACE) {
                    $phpcsFile->fixer->replaceToken($commentStart + 1, str_repeat(' ', $indent));
                    if ($tokens[$commentStart + 2]['code'] !== T_DOC_COMMENT_STAR) {
                        $phpcsFile->fixer->addContent($commentStart + 1, '* ');
                    }
                }
                $phpcsFile->fixer->endChangeset();
            }
        }
    }

    /**
     * Checks if there is no any other content before doc comment closing tag (for multiline doc comment).
     *
     * @param File $phpcsFile
     * @param int $commentEnd
     * @return void
     */
    private function checkBeforeClose(File $phpcsFile, $commentEnd)
    {
        $tokens = $phpcsFile->getTokens();

        $previous = $phpcsFile->findPrevious(T_DOC_COMMENT_WHITESPACE, $commentEnd - 1, null, true);
        if ($tokens[$previous]['line'] === $tokens[$commentEnd]['line']) {
            $error = 'The close comment tag must be the only content on the line.';
            $fix = $phpcsFile->addFixableError($error, $commentEnd, 'ContentBeforeClosingTag');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $content = $tokens[$commentEnd - 1]['content'];
                if (trim($content) . ' ' !== $content) {
                    $phpcsFile->fixer->replaceToken($commentEnd - 1, trim($content));
                }
                $phpcsFile->fixer->addNewlineBefore($commentEnd);
                $phpcsFile->fixer->endChangeset();
            }
        }
    }

    /**
     * Checks if there is no any other content after doc comment closing tag (for multiline doc comment).
     *
     * @param File $phpcsFile
     * @param int $commentStart
     * @param int $commentEnd
     * @return void
     */
    private function checkAfterClose(File $phpcsFile, $commentStart, $commentEnd)
    {
        $tokens = $phpcsFile->getTokens();

        $allowEmptyLineBefore = [
            T_NAMESPACE,
            T_USE,
        ];

        $prev = $phpcsFile->findPrevious(T_WHITESPACE, $commentStart - 1, null, true);
        $next = $phpcsFile->findNext(T_WHITESPACE, $commentEnd + 1, null, true);

        if (! $next) {
            $error = 'Doc comment is not allowed at the end of the file.';
            $phpcsFile->addError($error, $commentStart, 'DocCommentAtTheEndOfTheFile');
            return;
        }

        if ($tokens[$commentEnd]['line'] === $tokens[$next]['line']) {
            $error = 'The close comment tag must be the only content on the line.';
            $fix = $phpcsFile->addFixableError($error, $commentEnd, 'ContentAfterClosingTag');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $newLine = $commentEnd;
                if ($tokens[$commentEnd + 1]['code'] === T_WHITESPACE) {
                    $phpcsFile->fixer->replaceToken($commentEnd + 1, '');
                    $newLine++;
                }
                $phpcsFile->fixer->addNewline($newLine);
                $phpcsFile->fixer->endChangeset();
            }
        } elseif ($tokens[$prev]['code'] === T_OPEN_TAG) {
            if ($tokens[$next]['line'] === $tokens[$commentEnd]['line'] + 1) {
                $error = 'Missing blank line after file doc comment.';
                $fix = $phpcsFile->addFixableError($error, $commentEnd, 'MissingBlankLineAfter');

                if ($fix) {
                    $phpcsFile->fixer->addNewline($commentEnd);
                }
            }
        } elseif ($tokens[$next]['line'] > $tokens[$commentEnd]['line'] + 1
            && ! in_array($tokens[$next]['code'], $allowEmptyLineBefore, true)
        ) {
            $error = 'Additional blank lines found after doc comment.';
            $fix = $phpcsFile->addFixableError($error, $commentEnd + 2, 'BlankLinesAfter');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = $commentEnd + 1; $i < $next; $i++) {
                    if ($tokens[$i + 1]['line'] === $tokens[$next]['line']) {
                        break;
                    }

                    $phpcsFile->fixer->replaceToken($i, '');
                }
                $phpcsFile->fixer->endChangeset();
            }
        }
    }

    /**
     * Checks if there is exactly one space after doc comment opening tag,
     * and exactly one space before closing tag (for single line doc comment).
     *
     * @param File $phpcsFile
     * @param int $commentStart
     * @param int $commentEnd
     * @return void
     */
    private function checkSpacesInOneLineComment(File $phpcsFile, $commentStart, $commentEnd)
    {
        $tokens = $phpcsFile->getTokens();

        // Check, if there is exactly one space after opening tag.
        if ($tokens[$commentStart + 1]['code'] === T_DOC_COMMENT_WHITESPACE
            && $tokens[$commentStart + 1]['content'] !== ' '
        ) {
            $error = 'Expected 1 space after opening tag of one line doc block comment.';
            $fix = $phpcsFile->addFixableError($error, $commentStart + 1, 'InvalidSpacing');

            if ($fix) {
                $phpcsFile->fixer->replaceToken($commentStart + 1, ' ');
            }
        } elseif ($tokens[$commentStart + 1]['code'] !== T_DOC_COMMENT_WHITESPACE) {
            // This case is currently not supported.
            // Comment /**@var null $name; */ is not recognized as doc-block comment.
            $error = 'Expected 1 space after opening tag of one line doc block comment.';
            $fix = $phpcsFile->addFixableError($error, $commentStart, 'InvalidSpacing');

            if ($fix) {
                $phpcsFile->fixer->addContent($commentStart, ' ');
            }
        }

        // Check, if there is exactly one space before closing tag.
        $content = $tokens[$commentEnd - 1]['content'];
        if (trim($content) . ' ' !== $content) {
            $error = 'Expected 1 space before closing tag of one line doc block comment.';
            $fix = $phpcsFile->addFixableError($error, $commentEnd - 1, 'InvalidSpacing');

            if ($fix) {
                $phpcsFile->fixer->replaceToken($commentEnd - 1, trim($content) . ' ');
            }
        }
    }

    /**
     * Checks if there is one space after star in multiline doc comment.
     * More than one space is allowed, unless the line contains tag.
     *
     * TODO: needs to check with doctrine annotations
     *
     * @param File $phpcsFile
     * @param int $commentStart
     * @param int $commentEnd
     * @return void
     */
    private function checkSpacesAfterStar(File $phpcsFile, $commentStart, $commentEnd)
    {
        $tokens = $phpcsFile->getTokens();

        $next = $commentStart;
        $search = [T_DOC_COMMENT_STAR, T_DOC_COMMENT_CLOSE_TAG];
        while ($next = $phpcsFile->findNext($search, $next + 1, $commentEnd + 1)) {
            if ($tokens[$next]['code'] === T_DOC_COMMENT_STAR
                && $tokens[$next + 1]['code'] !== T_DOC_COMMENT_WHITESPACE
                || ($tokens[$next + 1]['code'] === T_DOC_COMMENT_WHITESPACE
                    && strpos($tokens[$next + 1]['content'], $phpcsFile->eolChar) === false)
            ) {
                if ($tokens[$next + 1]['code'] !== T_DOC_COMMENT_WHITESPACE) {
                    $error = 'There must be exactly one space between star and comment.';
                    $fix = $phpcsFile->addFixableError($error, $next, 'NoSpaceAfterStar');

                    if ($fix) {
                        $phpcsFile->fixer->addContent($next, ' ');
                    }
                } elseif ($tokens[$next + 1]['content'] !== ' '
                    && ($tokens[$next + 2]['content'][0] === '@'
                        || $tokens[$next + 1]['line'] === $tokens[$commentStart]['line'] + 1)
                ) {
                    $error = 'There must be exactly one space between star and comment.';
                    $fix = $phpcsFile->addFixableError($error, $next + 1, 'TooManySpacesAfterStar');

                    if ($fix) {
                        $phpcsFile->fixer->replaceToken($next + 1, ' ');
                    }
                }
            }
        }
    }

    /**
     * Doc comment cannot have empty line on the beginning of the comment, at the end of the comment,
     * and there is allowed only one empty line between two comment sections.
     *
     * @param File $phpcsFile
     * @param int $commentStart
     * @param int $commentEnd
     * @return void
     */
    private function checkBlankLinesInComment(File $phpcsFile, $commentStart, $commentEnd)
    {
        $tokens = $phpcsFile->getTokens();

        $empty = [
            T_DOC_COMMENT_WHITESPACE,
            T_DOC_COMMENT_STAR,
        ];

        // Additional blank lines at the beginning of doc block.
        $next = $phpcsFile->findNext($empty, $commentStart + 1, null, true);
        if ($tokens[$next]['line'] > $tokens[$commentStart]['line'] + 1) {
            $error = 'Additional blank lines found at the beginning of doc comment.';
            $fix = $phpcsFile->addFixableError($error, $commentStart + 2, 'SpacingBefore');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = $commentStart + 1; $i < $next; $i++) {
                    if ($tokens[$i + 1]['line'] === $tokens[$next]['line']) {
                        break;
                    }

                    $phpcsFile->fixer->replaceToken($i, '');
                }
                $phpcsFile->fixer->endChangeset();
            }
        }

        // Additional blank lines at the and of doc block.
        $previous = $phpcsFile->findPrevious($empty, $commentEnd - 1, null, true);
        if ($tokens[$previous]['line'] < $tokens[$commentEnd]['line'] - 1) {
            $error = 'Additional blank lines found at the end of doc comment.';
            $fix = $phpcsFile->addFixableError($error, $previous + 2, 'SpacingAfter');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = $previous + 1; $i < $commentEnd; $i++) {
                    if ($tokens[$i + 1]['line'] === $tokens[$commentEnd]['line']) {
                        break;
                    }

                    $phpcsFile->fixer->replaceToken($i, '');
                }
                $phpcsFile->fixer->endChangeset();
            }
        }

        // Check for double blank lines.
        $from = $phpcsFile->findNext($empty, $commentStart + 1, null, true);
        $to = $phpcsFile->findPrevious($empty, $commentEnd - 1, null, true);

        while ($next = $phpcsFile->findNext($empty, $from + 1, $to, true)) {
            if ($tokens[$next]['line'] > $tokens[$from]['line'] + 2) {
                $error = 'More than one blank line between parts of doc block.';
                $i = 0;
                while ($token = $phpcsFile->findNext(T_DOC_COMMENT_STAR, $from + 1, $next - 2)) {
                    if ($i++ > 0) {
                        $fix = $phpcsFile->addFixableError($error, $token, 'MultipleBlankLines');

                        if ($fix) {
                            $firstOnLine = $phpcsFile->findFirstOnLine($empty, $token);
                            for ($n = $firstOnLine; $n <= $token + 1; $n++) {
                                $phpcsFile->fixer->replaceToken($n, '');
                            }
                        }
                    }

                    $from = $token;
                }
            }

            $from = $next;
        }
    }

    /**
     * Checks indents of the comment (opening tag, lines with star, closing tag).
     *
     * @param File $phpcsFile
     * @param int $commentStart
     * @param int $commentEnd
     * @return void
     */
    private function checkCommentIndents(File $phpcsFile, $commentStart, $commentEnd)
    {
        $tokens = $phpcsFile->getTokens();

        $allowEmptyLineBefore = [
            T_NAMESPACE,
            T_USE,
        ];

        $next = $phpcsFile->findNext(T_WHITESPACE, $commentEnd + 1, null, true);

        // There is something exactly in the next line.
        if ($next && $tokens[$next]['line'] === $tokens[$commentEnd]['line'] + 1) {
            // Check indent of the next line.
            if ($tokens[$next - 1]['code'] === T_WHITESPACE
                && strpos($tokens[$next - 1]['content'], $phpcsFile->eolChar) === false
            ) {
                $indent = strlen($tokens[$next - 1]['content']);
            } else {
                $indent = 0;
            }
        } elseif (! $next
            || ($tokens[$next]['line'] > $tokens[$commentEnd]['line'] + 1
                && in_array($tokens[$next]['code'], $allowEmptyLineBefore, true))
        ) {
            $indent = 0;
        } else {
            return;
        }

        // The open tag is alone in the line.
        $previous = $phpcsFile->findPrevious(T_WHITESPACE, $commentStart - 1, null, true);
        if ($tokens[$previous]['line'] < $tokens[$commentStart]['line']) {
            // Check if comment starts with the same indent.
            $spaces = $tokens[$commentStart - 1];
            if ($spaces['code'] === T_WHITESPACE
                && strpos($spaces['content'], $phpcsFile->eolChar) === false
                && strlen($spaces['content']) !== $indent
            ) {
                $error = 'Invalid doc comment indent. Expected %d spaces; %d found';
                $data = [
                    $indent,
                    strlen($spaces['content']),
                ];
                $fix = $phpcsFile->addFixableError($error, $commentStart, 'InvalidIndent', $data);

                if ($fix) {
                    $phpcsFile->fixer->replaceToken($commentStart - 1, str_repeat(' ', $indent));
                }
            } elseif ($spaces['code'] === T_WHITESPACE
                && strpos($spaces['content'], $phpcsFile->eolChar) !== false
                && $indent > 0
            ) {
                $error = 'Invalid doc comment indent. Expected %d spaces; %d found';
                $data = [
                    $indent,
                    0,
                ];
                $fix = $phpcsFile->addFixableError($error, $commentStart, 'InvalidIndent', $data);

                if ($fix) {
                    $phpcsFile->fixer->replaceToken(
                        $commentStart - 1,
                        $phpcsFile->eolChar . str_repeat(' ', $indent)
                    );
                }
            }
        }

        // This is one-line doc comment.
        if ($tokens[$commentStart]['line'] === $tokens[$commentEnd]['line']) {
            return;
        }

        // Rest of the doc comment.
        $from = $commentStart;
        $search = [T_DOC_COMMENT_STAR, T_DOC_COMMENT_CLOSE_TAG];
        while ($next = $phpcsFile->findNext($search, $from + 1, $commentEnd + 1)) {
            if ($tokens[$next]['line'] !== $tokens[$from]['line']) {
                $spaces = $tokens[$next - 1];

                if (strpos($spaces['content'], $phpcsFile->eolChar) !== false) {
                    $error = 'Invalid doc comment indent. Expected %d spaces; %d found';
                    $data = [
                        $indent + 1,
                        0,
                    ];
                    $fix = $phpcsFile->addFixableError($error, $next, 'InvalidIndent', $data);

                    if ($fix) {
                        $phpcsFile->fixer->replaceToken($next - 1, $phpcsFile->eolChar . ' ');
                    }
                } elseif ($spaces['code'] === T_DOC_COMMENT_WHITESPACE
                    && strlen($spaces['content']) !== $indent + 1
                ) {
                    $error = 'Invalid doc comment indent. Expected %d spaces; %d found';
                    $data = [
                        $indent + 1,
                        strlen($spaces['content']),
                    ];
                    $fix = $phpcsFile->addFixableError($error, $next, 'InvalidIndent', $data);

                    if ($fix) {
                        $phpcsFile->fixer->replaceToken($next - 1, str_repeat(' ', $indent + 1));
                    }
                }
            }

            $from = $next;
        }
    }

    /**
     * Check if there is one blank line before comment tags.
     *
     * @param File $phpcsFile
     * @param int $commentStart
     * @return void
     */
    private function checkBlankLineBeforeTags(File $phpcsFile, $commentStart)
    {
        $tokens = $phpcsFile->getTokens();

        if (! $tokens[$commentStart]['comment_tags']) {
            return;
        }

        $tag = $tokens[$commentStart]['comment_tags'][0];
        $beforeTag = $phpcsFile->findPrevious(
            [T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STAR],
            $tag - 1,
            null,
            true
        );

        if ($tokens[$beforeTag]['code'] === T_DOC_COMMENT_STRING
            && $tokens[$beforeTag]['line'] === $tokens[$tag]['line'] - 1
        ) {
            $firstOnLine = $phpcsFile->findFirstOnLine([], $tag, true);

            $error = 'Missing blank line before comment tags.';
            $fix = $phpcsFile->addFixableError($error, $firstOnLine, 'MissingBlankLine');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->addNewlineBefore($firstOnLine);
                $phpcsFile->fixer->addContentBefore($firstOnLine, '*');
                $phpcsFile->fixer->endChangeset();
            }
        }
    }
}

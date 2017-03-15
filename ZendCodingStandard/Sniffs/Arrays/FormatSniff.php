<?php
namespace ZendCodingStandard\Sniffs\Arrays;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;

class FormatSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * The number of spaces code should be indented.
     *
     * @var int
     */
    public $indent = 4;

    /**
     * @return int[]
     */
    public function register()
    {
        return [T_OPEN_SHORT_ARRAY];
    }

    /**
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $arrayToken = $tokens[$stackPtr];

        $bracketOpener = $arrayToken['bracket_opener'];
        $bracketCloser = $arrayToken['bracket_closer'];

        if ($tokens[$bracketOpener]['line'] !== $tokens[$bracketCloser]['line']) {
            $this->multiLineArray($phpcsFile, $stackPtr);
            return;
        }

        $this->singleLineArray($phpcsFile, $stackPtr);
    }

    /**
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    private function multiLineArray(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $arrayToken = $tokens[$stackPtr];

        $bracketOpener = $arrayToken['bracket_opener'];
        $bracketCloser = $arrayToken['bracket_closer'];

        $firstContent = $phpcsFile->findNext(T_WHITESPACE, $bracketOpener + 1, null, true);
        if ($tokens[$firstContent]['code'] === T_CLOSE_SHORT_ARRAY) {
            $error = 'Empty array must be in one line.';
            $fix = $phpcsFile->addFixableError($error, $stackPtr);

            if ($fix) {
                $phpcsFile->fixer->replaceToken($bracketOpener + 1, '');
            }

            return;
        }

        $lastContent = $phpcsFile->findPrevious(T_WHITESPACE, $bracketCloser - 1, null, true);
        if ($tokens[$bracketCloser]['line'] > $tokens[$lastContent]['line'] + 1) {
            $error = 'Blank line found at the end of array';
            $fix = $phpcsFile->addFixableError($error, $bracketCloser - 1);

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $i = $lastContent + 1;
                while ($tokens[$i]['line'] !== $tokens[$bracketCloser]['line']) {
                    $phpcsFile->fixer->replaceToken($i, '');
                    ++$i;
                }
                $phpcsFile->fixer->addNewlineBefore($bracketCloser);
                $phpcsFile->fixer->endChangeset();
            }
        }

        $first = $phpcsFile->findFirstOnLine([], $bracketOpener, true);
        $indent = $tokens[$first]['code'] === T_WHITESPACE
            ? strlen($tokens[$first]['content'])
            : 0;

        $previousLine = $tokens[$bracketOpener]['line'];
        $next = $bracketOpener;
        while ($next = $phpcsFile->findNext(T_WHITESPACE, $next + 1, $bracketCloser, true)) {
            if ($previousLine === $tokens[$next]['line']) {
                if ($tokens[$next]['code'] !== T_COMMENT) {
                    $error = 'There must be one array element per line.';
                    $fix = $phpcsFile->addFixableError($error, $next);

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
                        || $previousLine === $tokens[$bracketOpener]['line'])
                ) {
                    $firstOnLine = $phpcsFile->findFirstOnLine([], $next, true);

                    $fix = $phpcsFile->addFixableError('Empty line is not allowed here.', $firstOnLine - 1);

                    if ($fix) {
                        $phpcsFile->fixer->replaceToken($firstOnLine - 1, '');
                    }
                }

                $expected = $indent + $this->indent;

                // Check array element indent.
                if ($firstOnLine = $phpcsFile->findFirstOnLine(T_WHITESPACE, $next)) {
                    $found = strlen($tokens[$firstOnLine]['content']);
                    if ($found !== $expected) {
                        $error = 'Invalid array element indent - expected %d spaces; %d found';
                        $data = [
                            $expected,
                            $found,
                        ];
                        $fix = $phpcsFile->addFixableError($error, $firstOnLine, 'ElementIndent', $data);

                        if ($fix) {
                            $phpcsFile->fixer->replaceToken($firstOnLine, str_repeat(' ', $expected));
                        }
                    }
                } else {
                    $error = 'Invalid array element indent - expected %d spaces; 0 found';
                    $fix = $phpcsFile->addFixableError($error, $next, 'ElementIndent', [$expected]);

                    if ($fix) {
                        $phpcsFile->fixer->addContentBefore($next, str_repeat(' ', $expected));
                    }
                }
            }

            if ($tokens[$next]['code'] === T_COMMENT
                && (substr($tokens[$next]['content'], 0, 2) === '//'
                    || substr($tokens[$next]['content'], 0, 1) === '#')
            ) {
                $end = $next;
            } else {
                $end = $phpcsFile->findEndOfStatement($next);
                if ($tokens[$end]['code'] === T_DOUBLE_ARROW) {
                    $end = $phpcsFile->findEndOfStatement($end);
                }
            }

            $previousLine = $tokens[$end]['line'];
            $next = $end;
        }

        if ($first = $phpcsFile->findFirstOnLine([], $bracketCloser, true)) {
            if ($first < $bracketCloser - 1) {
                $error = 'Array closing bracket should be in new line.';
                $fix = $phpcsFile->addFixableError($error, $bracketCloser);

                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();
                    if ($indent > 0) {
                        $phpcsFile->fixer->addContentBefore($bracketCloser, str_repeat(' ', $indent));
                    }
                    $phpcsFile->fixer->addNewlineBefore($bracketCloser);
                    $phpcsFile->fixer->endChangeset();
                }
            }
        }
    }

    /**
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    private function singleLineArray(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $arrayToken = $tokens[$stackPtr];

        $bracketOpener = $arrayToken['bracket_opener'];
        $bracketCloser = $arrayToken['bracket_closer'];

        // Single-line array - spaces before first element
        if ($tokens[$bracketOpener + 1]['code'] === T_WHITESPACE) {
            $error = sprintf(
                'Expected 0 spaces after array bracket opener; %d found',
                strlen($tokens[$bracketOpener + 1]['content'])
            );

            $fix = $phpcsFile->addFixableError($error, $bracketOpener + 1);

            if ($fix) {
                $phpcsFile->fixer->replaceToken($bracketOpener + 1, '');
            }
        }

        // Single-line array - spaces before last element
        if ($tokens[$bracketCloser - 1]['code'] === T_WHITESPACE) {
            $error = sprintf(
                'Expected 0 spaces before array bracket closer; %d found',
                strlen($tokens[$bracketCloser - 1]['content'])
            );

            $fix = $phpcsFile->addFixableError($error, $bracketCloser - 1);

            if ($fix) {
                $phpcsFile->fixer->replaceToken($bracketCloser - 1, '');
            }
        }
    }
}

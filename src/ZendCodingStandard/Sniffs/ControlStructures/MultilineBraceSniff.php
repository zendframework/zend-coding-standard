<?php
namespace ZendCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class MultilineBraceSniff implements Sniff
{
    /**
     * @var int
     */
    public $indent = 4;

    /**
     * @var array
     */
    private $skipStructures = [
        T_FUNCTION,
        T_CLOSURE,
    ];

    /**
     * @var array
     */
    private $functionCall = [
        T_STRING,
        T_VARIABLE,
        T_CLOSE_CURLY_BRACKET,
        T_CLOSE_PARENTHESIS,
    ];

    /**
     * @inheritDoc
     */
    public function register()
    {
        return [T_OPEN_CURLY_BRACKET];
    }

    /**
     * @inheritDoc
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $opener = $tokens[$stackPtr];
        if (! isset($opener['scope_condition'])) {
            return;
        }

        $scopeCondition = $tokens[$opener['scope_condition']];

        if ($scopeCondition['line'] === $opener['line']) {
            return;
        }

        if (in_array($scopeCondition['code'], $this->skipStructures, true)) {
            return;
        }

        $parenthesis = $phpcsFile->findPrevious(Tokens::$emptyTokens, $stackPtr - 1, null, true);
        if ($tokens[$parenthesis]['code'] !== T_CLOSE_PARENTHESIS) {
            return;
        }

        $prev = $phpcsFile->findPrevious(
            Tokens::$emptyTokens + [T_CLOSE_PARENTHESIS => T_CLOSE_PARENTHESIS],
            $parenthesis - 1,
            null,
            true
        );
        if ($scopeCondition['line'] === $tokens[$prev]['line']) {
            $error = 'Closing parenthesis must be in the same line as control structure.';
            $fix = $phpcsFile->addFixableError($error, $parenthesis, 'UnnecessaryLineBreak');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = $prev + 1; $i < $parenthesis; ++$i) {
                    if ($tokens[$i]['code'] === T_WHITESPACE) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }
                }
                $phpcsFile->fixer->endChangeset();
            }

            return;
        }

        $token = $tokens[$parenthesis]['parenthesis_opener'];
        while ($token = $phpcsFile->findNext(T_CLOSE_PARENTHESIS, $token + 1, $parenthesis)) {
            $prevCloser = $phpcsFile->findPrevious(Tokens::$emptyTokens, $token - 1, null, true);
            if ($tokens[$prevCloser]['line'] === $tokens[$token]['line']) {
                continue;
            }

            $open = $tokens[$token]['parenthesis_opener'];
            $prevOpener = $phpcsFile->findPrevious(Tokens::$emptyTokens, $open - 1, null, true);
            $first = $phpcsFile->findFirstOnLine([], $open, true);

            if (in_array($tokens[$prevOpener]['code'], $this->functionCall, true)) {
                // It is a function call.
                if ($tokens[$open]['line'] < $tokens[$token]['line']
                    && $tokens[$token - 1]['line'] < $tokens[$token]['line']
                    && $tokens[$first]['code'] === T_WHITESPACE
                ) {
                    $expectedIndentWidth = strlen($tokens[$first]['content']);
                    $error = 'Multi-line function closing parenthesis not indented correctly;'
                        . ' expected %d spaces but found 0';
                    $data = [
                        $expectedIndentWidth,
                    ];
                    $fix = $phpcsFile->addFixableError($error, $token, 'ClosingParenthesisMultiLineFunction', $data);

                    if ($fix) {
                        $phpcsFile->fixer->addContentBefore($token, str_repeat(' ', $expectedIndentWidth));
                    }
                }
            } else {
                $error = 'Invalid closing parenthesis position.';
                $fix = $phpcsFile->addFixableError($error, $token, 'InvalidClosingParenthesisPosition');

                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->addContent($prevCloser, $tokens[$token]['content']);
                    $phpcsFile->fixer->replaceToken($token, '');
                    $t = $token + 1;
                    while ($tokens[$t]['code'] === T_WHITESPACE
                        && $tokens[$t]['line'] === $tokens[$token]['line']
                    ) {
                        $phpcsFile->fixer->replaceToken($t, '');

                        ++$t;
                    }
                    $phpcsFile->fixer->endChangeset();
                }
            }
        }

        $prev = $parenthesis;
        $squashClose = false;
        while (($prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $prev - 1, null, true))
            && $tokens[$prev]['code'] === T_CLOSE_PARENTHESIS
            && $tokens[$prev]['line'] > $scopeCondition['line']
            && $tokens[$tokens[$prev]['parenthesis_opener']]['line'] === $scopeCondition['line']
            && ! $phpcsFile->findFirstOnLine(
                Tokens::$emptyTokens + [T_CLOSE_PARENTHESIS => T_CLOSE_PARENTHESIS],
                $prev,
                true
            )
        ) {
            if ($tokens[$prev]['line'] <= $tokens[$parenthesis]['line'] - 1) {
                $error = 'Invalid closing parenthesis position.';
                $fix = $phpcsFile->addFixableError($error, $prev, 'InvalidClosingParenthesisPosition');

                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = $prev + 1; $i < $parenthesis; ++$i) {
                        if ($tokens[$i]['code'] === T_WHITESPACE) {
                            $phpcsFile->fixer->replaceToken($i, '');
                        }
                    }
                    $phpcsFile->fixer->endChangeset();
                }
            } elseif ($tokens[$prev + 1]['code'] === T_WHITESPACE) {
                $error = 'Unexpected whitespace before closing parenthesis.';
                $fix = $phpcsFile->addFixableError($error, $prev + 1, 'UnexpectedSpacesBeforeClosingParenthesis');

                if ($fix) {
                    $phpcsFile->fixer->replaceToken($prev + 1, '');
                }
            }

            $squashClose = true;
        }

        // Find indent before control structure
        $indent = '';
        $firstOnLine = $phpcsFile->findFirstOnLine([], $opener['scope_condition'], true);
        if ($tokens[$firstOnLine]['code'] === T_WHITESPACE) {
            $indent = $tokens[$firstOnLine]['content'];
        }

        if (! $squashClose) {
            if ($tokens[$prev]['line'] <= $tokens[$parenthesis]['line'] - 1) {
                // Check indent
                $whitespace = $phpcsFile->findFirstOnLine(T_WHITESPACE, $parenthesis);
                if ($whitespace && $tokens[$whitespace]['content'] !== $indent) {
                    // Invalid indent before parenthesis
                    $error = 'Invalid indent before closing parenthesis.';
                    $fix = $phpcsFile->addFixableError($error, $whitespace, 'InvalidIndentBeforeParenthesis');

                    if ($fix) {
                        $phpcsFile->fixer->replaceToken($whitespace, $indent);
                    }
                } elseif (! $whitespace && $indent) {
                    // Missing indent before closing parenthesis
                    $error = 'Missing indent before closing parenthesis.';
                    $fix = $phpcsFile->addFixableError($error, $parenthesis, 'MissingIndentBeforeParenthesis');

                    if ($fix) {
                        $phpcsFile->fixer->addContentBefore($parenthesis, $indent);
                    }
                }
            } else {
                // Needed new line before parenthesis
                $error = 'Closing parentheses must be in new line.';
                $fix = $phpcsFile->addFixableError($error, $parenthesis, 'NewLine');

                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->addContentBefore($parenthesis, $indent);
                    $phpcsFile->fixer->addNewlineBefore($parenthesis);
                    $phpcsFile->fixer->endChangeset();
                }
            }
        }

        // Check indent of each line
        $line = $scopeCondition['line'];
        $token = $opener['scope_condition'];
        $depth = 0;
        do {
            while ($tokens[$token]['line'] === $line) {
                if ($tokens[$token]['code'] === T_OPEN_PARENTHESIS) {
                    $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $token - 1, null, true);
                    if (in_array($tokens[$prev]['code'], $this->functionCall, true)) {
                        $token = $tokens[$token]['parenthesis_closer'] + 1;
                        $line = $tokens[$token]['line'];
                        continue;
                    }
                    ++$depth;
                } elseif ($tokens[$token]['code'] === T_CLOSE_PARENTHESIS) {
                    --$depth;
                }

                ++$token;
            }
            $line = $tokens[$token]['line'];

            if ($line >= $opener['line']) {
                break;
            }

            $first = $phpcsFile->findNext(Tokens::$emptyTokens, $token, null, true);
            if ($tokens[$first]['code'] === T_CLOSE_PARENTHESIS) {
                continue;
            } elseif (in_array(
                $tokens[$first]['code'],
                Tokens::$comparisonTokens + [T_INSTANCEOF => T_INSTANCEOF],
                true
            )) {
                $expectedIndentWidth = strlen($indent) + ($depth + 1) * $this->indent;
            } else {
                $expectedIndentWidth = strlen($indent) + $depth * $this->indent;
            }

            if ($tokens[$token]['code'] === T_WHITESPACE) {
                $indentWidth = strlen($tokens[$token]['content']);

                if ($indentWidth !== $expectedIndentWidth) {
                    $error = 'Invalid indent. Expected %d spaces, found %d';
                    $data = [
                        $expectedIndentWidth,
                        $indentWidth,
                    ];
                    $fix = $phpcsFile->addFixableError($error, $token, 'InvalidIndent', $data);

                    if ($fix) {
                        $phpcsFile->fixer->replaceToken($token, str_repeat(' ', $expectedIndentWidth));
                    }
                }
            } else {
                // Missing indent
                $error = 'Missing line indent. Expected %d spaces, found 0';
                $data = [$expectedIndentWidth];
                $fix = $phpcsFile->addFixableError($error, $token, 'MissingIndent', $data);

                if ($fix) {
                    $phpcsFile->fixer->addContentBefore($token, str_repeat(' ', $expectedIndentWidth));
                }
            }
        } while ($line < $opener['line']);
    }
}

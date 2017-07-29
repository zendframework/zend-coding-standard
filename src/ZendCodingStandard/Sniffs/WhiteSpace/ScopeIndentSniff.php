<?php
namespace ZendCodingStandard\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use function ceil;
use function in_array;
use function ltrim;
use function max;
use function preg_match;
use function str_repeat;
use function strlen;
use function strpos;
use function substr;

class ScopeIndentSniff implements Sniff
{
    public $indent = 4;

    public $alignObjectOperators = true;

    private $controlStructures = [
        T_IF => T_IF,
        T_ELSEIF => T_ELSEIF,
        T_WHILE => T_WHILE,
        T_FOR => T_FOR,
        T_FOREACH => T_FOREACH,
    ];

    private $endOfStatement = [
        T_SEMICOLON,
        T_CLOSE_CURLY_BRACKET,
        T_OPEN_CURLY_BRACKET,
        T_OPEN_TAG,
        T_COLON,
        T_GOTO_LABEL,
        T_COMMA,
        T_OPEN_PARENTHESIS,
        T_OPEN_SHORT_ARRAY,
    ];

    private $caseEndToken = [
        T_BREAK,
        T_CONTINUE,
        T_RETURN,
        T_THROW,
        T_EXIT,
    ];

    private $breakToken;

    private $functionToken;

    public function __construct()
    {
        $this->breakToken = Tokens::$operators
            + Tokens::$assignmentTokens
            + Tokens::$booleanOperators
            + Tokens::$comparisonTokens
            + [
                T_SEMICOLON => T_SEMICOLON,
                T_OPEN_PARENTHESIS => T_OPEN_PARENTHESIS,
                T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET,
                T_OPEN_SHORT_ARRAY => T_OPEN_SHORT_ARRAY,
                T_ARRAY => T_ARRAY,
                T_COMMA => T_COMMA,
                T_INLINE_ELSE => T_INLINE_ELSE,
                T_INLINE_THEN => T_INLINE_THEN,
                T_STRING_CONCAT => T_STRING_CONCAT,
            ];

        $this->functionToken = Tokens::$functionNameTokens
            + [
                T_SELF => T_SELF,
                T_STATIC => T_STATIC,
                T_VARIABLE => T_VARIABLE,
                T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET,
                T_CLOSE_PARENTHESIS => T_CLOSE_PARENTHESIS,
                T_USE => T_USE,
                T_CLOSURE => T_CLOSURE,
                T_ARRAY => T_ARRAY,
            ];
    }

    /**
     * @inheritDoc
     */
    public function register()
    {
        return [
            T_OPEN_TAG,
        ];
    }

    /**
     * @inheritDoc
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $depth = 0;
        $extras = [];
        $previousIndent = null;

        // calculate indent of php open tag
        $html = $phpcsFile->findFirstOnLine(T_INLINE_HTML, $stackPtr);
        $trimmed = ltrim($tokens[$html]['content']);
        if ($html === false || $trimmed === '') {
            $extraIndent = $tokens[$stackPtr]['column'] - 1;
        } else {
            $extraIndent = strlen($tokens[$html]['content']) - strlen($trimmed);
        }

        for ($i = $stackPtr + 1; $i < $phpcsFile->numTokens; ++$i) {
            if (in_array($tokens[$i]['code'], Tokens::$booleanOperators, true)) {
                $next = $phpcsFile->findNext(
                    Tokens::$emptyTokens + [T_OPEN_PARENTHESIS => T_OPEN_PARENTHESIS],
                    $i + 1,
                    null,
                    true
                );

                if ($tokens[$next]['line'] > $tokens[$i]['line']) {
                    $error = 'Boolean operator found at the end of the line.';
                    $fix = $phpcsFile->addFixableError($error, $i, 'BooleanOperatorAtTheEnd');

                    if ($fix) {
                        $lastNonEmpty = $phpcsFile->findPrevious(Tokens::$emptyTokens, $next - 1, null, true);
                        $string = $phpcsFile->getTokensAsString($i, $lastNonEmpty - $i + 1);

                        if (substr($string, -1) !== '(') {
                            $string .= ' ';
                        }

                        $phpcsFile->fixer->beginChangeset();
                        $j = $i - 1;
                        while ($tokens[$j]['code'] === T_WHITESPACE) {
                            $phpcsFile->fixer->replaceToken($j, '');
                            --$j;
                        }
                        for ($j = $i; $j <= $lastNonEmpty; ++$j) {
                            $phpcsFile->fixer->replaceToken($j, '');
                        }
                        $phpcsFile->fixer->addContentBefore($next, $string);
                        $phpcsFile->fixer->endChangeset();
                    }

                    continue;
                }
            }

            // skip some tags
            if ($tokens[$i]['code'] === T_INLINE_HTML) {
                // || $tokens[$i]['code'] === T_CLOSE_TAG
                // || $tokens[$i]['code'] === T_OPEN_TAG
                continue;
            }

            if (($tokens[$i]['code'] === T_CONSTANT_ENCAPSED_STRING
                    || $tokens[$i]['code'] === T_DOUBLE_QUOTED_STRING)
                && $tokens[$i - 1]['code'] === $tokens[$i]['code']
            ) {
                continue;
            }

            // || $tokens[$i]['code'] === T_ANON_CLASS
            if ($tokens[$i]['code'] === T_CLASS) {
                $i = $tokens[$i]['scope_opener'];
                continue;
            }

            // @todo: multi-open-tags
            if ($tokens[$i]['code'] === T_OPEN_TAG) {
                // $error = 'This sniff does not support files with multiple PHP open tags.';
                // $phpcsFile->addError($error, $i, 'UnsupportedFile');
                // return $phpcsFile->numTokens;
                // if ($depth ===  0) {
                $extraIndent = max($tokens[$i]['column'] - 1 - ($depth * $this->indent), 0);
                $extraIndent = (int) (ceil($extraIndent / $this->indent) * $this->indent);
                // }
                continue;
            }

            // skip doc block comment
            if ($tokens[$i]['code'] === T_DOC_COMMENT_OPEN_TAG) {
                $i = $tokens[$i]['comment_closer'];
                continue;
            }

            // skip heredoc/nowdoc
            if ($tokens[$i]['code'] === T_START_HEREDOC
                || $tokens[$i]['code'] === T_START_NOWDOC
            ) {
                $i = $tokens[$i]['scope_closer'];
                continue;
            }

            if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
                if (empty($tokens[$i]['parenthesis_owner'])) {
                    $parenthesisOwner = $phpcsFile->findPrevious(Tokens::$emptyTokens, $i - 1, null, true);
                } else {
                    $parenthesisOwner = $tokens[$i]['parenthesis_owner'];
                }

                if (in_array($tokens[$parenthesisOwner]['code'], $this->controlStructures, true)) {
                    $i = $tokens[$i]['parenthesis_closer'];
                    continue;
                }
            }

            if (isset($extras[$i])) {
                $extraIndent -= $extras[$i];
                unset($extras[$i]);
            }

            if ($tokens[$i]['code'] === T_OBJECT_OPERATOR) {
                $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $i - 1, null, true);

                if ($tokens[$prev]['line'] === $tokens[$i]['line']) {
                    if (($prevObjectOperator = $this->hasPrevObjectOperator($phpcsFile, $i))
                        && $tokens[$prevObjectOperator]['line'] < $tokens[$i]['line']
                    ) {
                        // add line break before
                        $error = 'Object operator must be in new line';
                        $fix = $phpcsFile->addFixableError($error, $i, 'ObjectOperator');

                        if ($fix) {
                            $phpcsFile->fixer->addNewlineBefore($i);
                        }
                    }

                    $next = $phpcsFile->findNext(
                        Tokens::$emptyTokens + [
                            T_STRING,
                            T_VARIABLE,
                            T_OPEN_CURLY_BRACKET,
                            T_CLOSE_CURLY_BRACKET,
                        ],
                        $i + 1,
                        null,
                        true
                    );

                    if ($tokens[$next]['code'] === T_OPEN_PARENTHESIS
                        && $tokens[$tokens[$next]['parenthesis_closer']]['line'] > $tokens[$next]['line']
                    ) {
                        $after = $phpcsFile->findNext(
                            Tokens::$emptyTokens,
                            $tokens[$next]['parenthesis_closer'] + 1,
                            null,
                            true
                        );
                        if ($tokens[$after]['code'] === T_OBJECT_OPERATOR) {
                            $column = $tokens[$i]['column'];
                            $newEI = $column - 1 - $extraIndent - $tokens[$i]['level'] * $this->indent;
                            $extraIndent += $newEI;
                            if (isset($extras[$after])) {
                                $extras[$after] += $newEI;
                            } else {
                                $extras[$after] = $newEI;
                            }
                        }
                    }
                }
            }

            if ($tokens[$i]['column'] === 1
                && ($next = $phpcsFile->findNext(T_WHITESPACE, $i, null, true))
                && $tokens[$next]['line'] === $tokens[$i]['line']
            ) {
                $depth = $tokens[$next]['level'];

                $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $i - 1, null, true);

                $expectedIndent = $depth * $this->indent;
                if (in_array($tokens[$next]['code'], $this->caseEndToken, true)
                    && isset($tokens[$next]['scope_closer'])
                    && $tokens[$next]['scope_closer'] === $next
                ) {
                    $endOfStatement = $phpcsFile->findEndOfStatement($next);
                    if (isset($extras[$endOfStatement])) {
                        $extras[$endOfStatement] += $this->indent;
                    } else {
                        $extras[$endOfStatement] = $this->indent;
                    }

                    $extraIndent += $this->indent;
                } elseif ($tokens[$next]['code'] === T_CLOSE_PARENTHESIS) {
                    if (isset($extras[$next])) {
                        $extraIndent -= $extras[$next];
                        unset($extras[$next]);
                    }

                    $opener = $tokens[$next]['parenthesis_opener'];
                    $owner = $phpcsFile->findPrevious(Tokens::$emptyTokens, $opener - 1, null, true);

                    // if it is not a function call
                    if (! in_array($tokens[$owner]['code'], $this->functionToken, true)) {
                        $error = 'Closing parenthesis must be in previous line.';
                        $fix = $phpcsFile->addFixableError($error, $next, 'ClosingParenthesis');

                        if ($fix) {
                            $semicolon = $phpcsFile->findNext(Tokens::$emptyTokens, $next + 1, null, true);

                            $phpcsFile->fixer->beginChangeset();
                            $phpcsFile->fixer->replaceToken($next, '');
                            $phpcsFile->fixer->addContent($prev, $tokens[$next]['content']);
                            if ($tokens[$semicolon]['code'] === T_SEMICOLON) {
                                $phpcsFile->fixer->addContent($prev, ';');
                                $phpcsFile->fixer->replaceToken($semicolon, '');
                                $j = $semicolon + 1;
                            } else {
                                $j = $next + 1;
                            }
                            while ($tokens[$j]['code'] === T_WHITESPACE) {
                                $phpcsFile->fixer->replaceToken($j, '');
                                ++$j;

                                if ($tokens[$j]['line'] > $tokens[$next]['line']) {
                                    break;
                                }
                            }
                            $phpcsFile->fixer->endChangeset();
                        }

                        continue;
                    }
                } elseif ($tokens[$next]['code'] === T_CLOSE_SHORT_ARRAY) {
                    if (isset($extras[$next])) {
                        $extraIndent -= $extras[$next];
                        unset($extras[$next]);
                    }
                } elseif ($tokens[$next]['code'] === T_OBJECT_OPERATOR) {
                    if (isset($extras[$next])) {
                        $extraIndent -= $extras[$next];
                        unset($extras[$next]);
                    }

                    $np = $this->np($phpcsFile, $next);
                    if ($fp = $this->fp($phpcsFile, $next)) {
                        $newEI = $fp - 1 - $expectedIndent - $extraIndent;
                        $extraIndent += $newEI;
                        if (isset($extras[$np])) {
                            $extras[$np] += $newEI;
                        } else {
                            $extras[$np] = $newEI;
                        }
                    } else {
                        $extraIndent += $this->indent;
                        if (isset($extras[$np])) {
                            $extras[$np] += $this->indent;
                        } else {
                            $extras[$np] = $this->indent;
                        }
                    }
                } elseif ($tokens[$next]['code'] === T_INLINE_THEN) {
                    $expectedIndent = $previousIndent - $extraIndent + $this->indent;
                } elseif ($tokens[$next]['code'] === T_INLINE_ELSE) {
                    $count = 0;
                    $t = $i;
                    while ($t = $phpcsFile->findPrevious([T_INLINE_THEN, T_INLINE_ELSE], $t - 1)) {
                        if ($tokens[$t]['code'] === T_INLINE_ELSE) {
                            ++$count;
                        } else {
                            --$count;

                            if ($count < 0) {
                                break;
                            }
                        }
                    }

                    $first = $phpcsFile->findFirstOnLine([], $t, true);
                    if ($tokens[$first]['code'] !== T_WHITESPACE) {
                        $expectedIndent = $this->indent;
                    } else {
                        $expectedIndent = strlen($tokens[$first]['content']) - $extraIndent;

                        $firstNonEmpty = $phpcsFile->findFirstOnLine(Tokens::$emptyTokens, $t, true);
                        if ($t !== $firstNonEmpty) {
                            $expectedIndent += $this->indent;
                        }
                    }
                } elseif (! in_array($tokens[$prev]['code'], $this->endOfStatement, true)
                    && $tokens[$next]['code'] !== T_OPEN_CURLY_BRACKET
                ) {
                    if ($expectedIndent + $extraIndent <= $previousIndent) {
                        $expectedIndent = ($depth + 1) * $this->indent;
                    }
                }

                $expectedIndent += $extraIndent;
                $previousIndent = $expectedIndent;

                if ($tokens[$i]['code'] === T_WHITESPACE
                    && strpos($tokens[$i]['content'], $phpcsFile->eolChar) === false
                    && strlen($tokens[$i]['content']) !== $expectedIndent
                ) {
                    $error = 'Invalid indent. Expected %d spaces, found %d';
                    $data = [
                        $expectedIndent,
                        strlen($tokens[$i]['content']),
                    ];
                    $fix = $phpcsFile->addFixableError($error, $i, 'InvalidIndent', $data);

                    if ($fix) {
                        $phpcsFile->fixer->replaceToken($i, str_repeat(' ', max($expectedIndent, 0)));
                    }
                } elseif ($tokens[$i]['code'] === T_COMMENT
                    && preg_match('/^(\s*)\*/', $tokens[$i]['content'], $match)
                ) {
                    if (strlen($match[1]) !== $expectedIndent + 1) {
                        $error = 'Invalid comment indent. Expected %d spaces, found %d';
                        $data = [
                            $expectedIndent + 1,
                            strlen($match[1]),
                        ];
                        $fix = $phpcsFile->addFixableError($error, $i, 'CommentIndent', $data);

                        if ($fix) {
                            $phpcsFile->fixer->replaceToken(
                                $i,
                                str_repeat(' ', max($expectedIndent, 0) + 1) . ltrim($tokens[$i]['content'])
                            );
                        }
                    }
                } elseif ($tokens[$i]['code'] !== T_WHITESPACE
                    && $expectedIndent
                    && ($tokens[$i]['code'] !== T_COMMENT
                        || preg_match('/^\s*(\/\/|#)/', $tokens[$i]['content']))
                ) {
                    $error = 'Missing indent. Expected %d spaces';
                    $data = [$expectedIndent];
                    $fix = $phpcsFile->addFixableError($error, $i, 'MissingIndent', $data);

                    if ($fix) {
                        $phpcsFile->fixer->addContentBefore($i, str_repeat(' ', max($expectedIndent, 0)));
                    }
                }
            }

            // count extra indent
            $ei = 0;
            if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS
                || $tokens[$i]['code'] === T_OPEN_SHORT_ARRAY
                || ($tokens[$i]['code'] === T_OPEN_CURLY_BRACKET
                    && isset($tokens[$i]['scope_closer']))
            ) {
                switch ($tokens[$i]['code']) {
                    case T_OPEN_PARENTHESIS:
                        $key = 'parenthesis_closer';
                        break;
                    case T_OPEN_SHORT_ARRAY:
                        $key = 'bracket_closer';
                        break;
                    default:
                        $key = 'scope_closer';
                        break;

                }
                $xEnd = $tokens[$i][$key];

                // no extra indent if closing parenthesis/bracket is in the same line
                if ($tokens[$i]['line'] === $tokens[$xEnd]['line']) {
                    continue;
                }

                // if there is another open bracket in that line, skip current one.
                $another = $i;
                $openTags = [T_OPEN_PARENTHESIS, T_OPEN_SHORT_ARRAY, T_OPEN_CURLY_BRACKET];
                while (($another = $phpcsFile->findNext($openTags, $another + 1))
                    && $tokens[$another]['line'] === $tokens[$i]['line']
                ) {
                    if (($tokens[$another]['code'] === T_OPEN_PARENTHESIS
                            && $tokens[$tokens[$another]['parenthesis_closer']]['line'] > $tokens[$another]['line'])
                        || ($tokens[$another]['code'] === T_OPEN_SHORT_ARRAY
                            && $tokens[$tokens[$another]['bracket_closer']]['line'] > $tokens[$another]['line'])
                        || ($tokens[$another]['code'] === T_OPEN_CURLY_BRACKET
                            && isset($tokens[$another]['scope_closer'])
                            && $tokens[$tokens[$another]['scope_closer']]['line'] > $tokens[$another]['line'])
                    ) {
                        continue 2;
                    }
                }

                $first = $phpcsFile->findFirstOnLine(T_WHITESPACE, $i, true);

                $firstInNextLine = $i;
                while ($tokens[$firstInNextLine]['line'] === $tokens[$i]['line']
                    || $tokens[$firstInNextLine]['code'] === T_WHITESPACE
                ) {
                    ++$firstInNextLine;
                }

                if ($tokens[$first]['level'] === $tokens[$firstInNextLine]['level']
                    && $tokens[$firstInNextLine]['code'] !== T_CLOSE_CURLY_BRACKET
                ) {
                    $ei += $this->indent;
                }

                $next = $phpcsFile->findNext(Tokens::$emptyTokens, $i + 1, null, true);

                if ($tokens[$next]['line'] > $tokens[$i]['line']) {
                    // current line indent
                    $whitespace = $phpcsFile->findFirstOnLine([], $i, true);
                    if ($tokens[$whitespace]['code'] === T_WHITESPACE) {
                        $sum = strlen($tokens[$whitespace]['content'])
                            - $tokens[$first]['level'] * $this->indent
                            - $extraIndent;

                        if ($sum > 0) {
                            $ei += $sum;
                        }
                    }
                }
            }

            if ($ei) {
                $extraIndent += $ei;
                if (isset($extras[$xEnd])) {
                    $extras[$xEnd] += $ei;
                } else {
                    $extras[$xEnd] = $ei;
                }
            }
        }

        return $phpcsFile->numTokens;
    }

    /**
     * @param File $phpcsFile
     * @param int $ptr
     * @return int|null
     */
    private function fp(File $phpcsFile, $ptr)
    {
        if ($this->alignObjectOperators) {
            $tokens = $phpcsFile->getTokens();

            while (--$ptr) {
                if ($tokens[$ptr]['code'] === T_CLOSE_PARENTHESIS) {
                    $ptr = $tokens[$ptr]['parenthesis_opener'];
                } elseif ($tokens[$ptr]['code'] === T_CLOSE_CURLY_BRACKET) {
                    $ptr = $tokens[$ptr]['bracket_opener'];
                } elseif ($tokens[$ptr]['code'] === T_OBJECT_OPERATOR) {
                    return $tokens[$ptr]['column'];
                } elseif ($tokens[$ptr]['code'] === T_SEMICOLON) {
                    break;
                }
            }
        }

        return null;
    }

    /**
     * @param File $phpcsFile
     * @param int $ptr
     * @return int|null
     */
    private function np(File $phpcsFile, $ptr)
    {
        $tokens = $phpcsFile->getTokens();

        while (++$ptr) {
            if ($tokens[$ptr]['code'] === T_OPEN_PARENTHESIS) {
                $ptr = $tokens[$ptr]['parenthesis_closer'];
            } elseif ($tokens[$ptr]['code'] === T_OPEN_CURLY_BRACKET) {
                $ptr = $tokens[$ptr]['bracket_closer'];
            } elseif ($tokens[$ptr]['code'] === T_OBJECT_OPERATOR
                || $tokens[$ptr]['code'] === T_SEMICOLON
            ) {
                return $ptr;
            }
        }

        return null;
    }

    /**
     * @param File $phpcsFile
     * @param int $ptr
     * @return int|null
     */
    private function hasPrevObjectOperator(File $phpcsFile, $ptr)
    {
        $tokens = $phpcsFile->getTokens();

        while (--$ptr) {
            if ($tokens[$ptr]['code'] === T_CLOSE_PARENTHESIS) {
                $ptr = $tokens[$ptr]['parenthesis_opener'];
            } elseif ($tokens[$ptr]['code'] === T_CLOSE_CURLY_BRACKET) {
                $ptr = $tokens[$ptr]['bracket_opener'];
            } elseif ($tokens[$ptr]['code'] === T_OBJECT_OPERATOR) {
                return $ptr;
            } elseif (in_array($tokens[$ptr]['code'], $this->breakToken, true)) {
                break;
            }
        }

        return null;
    }
}

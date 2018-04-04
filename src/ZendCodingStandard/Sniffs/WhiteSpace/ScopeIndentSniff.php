<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYRIGHT.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

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

use const T_ARRAY;
use const T_BREAK;
use const T_CATCH;
use const T_CLASS;
use const T_CLOSE_CURLY_BRACKET;
use const T_CLOSE_PARENTHESIS;
use const T_CLOSE_SHORT_ARRAY;
use const T_CLOSE_SQUARE_BRACKET;
use const T_CLOSURE;
use const T_COLON;
use const T_COMMA;
use const T_COMMENT;
use const T_CONSTANT_ENCAPSED_STRING;
use const T_CONTINUE;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_DOUBLE_ARROW;
use const T_DOUBLE_QUOTED_STRING;
use const T_ELSEIF;
use const T_EXIT;
use const T_FOR;
use const T_FOREACH;
use const T_FUNCTION;
use const T_GOTO_LABEL;
use const T_IF;
use const T_INLINE_ELSE;
use const T_INLINE_HTML;
use const T_INLINE_THEN;
use const T_OBJECT_OPERATOR;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_PARENTHESIS;
use const T_OPEN_SHORT_ARRAY;
use const T_OPEN_SQUARE_BRACKET;
use const T_OPEN_TAG;
use const T_RETURN;
use const T_SELF;
use const T_SEMICOLON;
use const T_START_HEREDOC;
use const T_START_NOWDOC;
use const T_STATIC;
use const T_STRING;
use const T_STRING_CONCAT;
use const T_SWITCH;
use const T_THROW;
use const T_USE;
use const T_VARIABLE;
use const T_WHILE;
use const T_WHITESPACE;

class ScopeIndentSniff implements Sniff
{
    /**
     * @var int
     */
    public $indent = 4;

    /**
     * @var bool
     */
    public $alignObjectOperators = true;

    /**
     * @var int[]
     */
    private $controlStructures = [
        T_IF => T_IF,
        T_ELSEIF => T_ELSEIF,
        T_SWITCH => T_SWITCH,
        T_WHILE => T_WHILE,
        T_FOR => T_FOR,
        T_FOREACH => T_FOREACH,
        T_CATCH => T_CATCH,
    ];

    /**
     * @var int[]
     */
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

    /**
     * @var int[]
     */
    private $caseEndToken = [
        T_BREAK,
        T_CONTINUE,
        T_RETURN,
        T_THROW,
        T_EXIT,
    ];

    /**
     * @var int[]
     */
    private $breakToken;

    /**
     * @var int[]
     */
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
            + $this->controlStructures
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
     * @return int[]
     */
    public function register() : array
    {
        return [T_OPEN_TAG];
    }

    /**
     * @param int $stackPtr
     * @return null|int
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
                // return $phpcsFile->numTokens + 1;
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

            if (isset($extras[$i])) {
                $extraIndent -= $extras[$i];
                unset($extras[$i]);
            }

            // check if closing parenthesis is in the same line as control structure
            if ($tokens[$i]['code'] === T_OPEN_CURLY_BRACKET
                && isset($tokens[$i]['scope_condition'])
                && ($scopeCondition = $tokens[$tokens[$i]['scope_condition']])
                && ! in_array($scopeCondition['code'], [T_FUNCTION, T_CLOSURE], true)
                && ($parenthesis = $phpcsFile->findPrevious(Tokens::$emptyTokens, $i - 1, null, true))
                && $tokens[$parenthesis]['code'] === T_CLOSE_PARENTHESIS
                && $tokens[$parenthesis]['line'] > $scopeCondition['line']
            ) {
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
                        for ($j = $prev + 1; $j < $parenthesis; ++$j) {
                            if ($tokens[$j]['code'] === T_WHITESPACE) {
                                $phpcsFile->fixer->replaceToken($j, '');
                            }
                        }
                        $phpcsFile->fixer->endChangeset();
                    }
                }
            }

            // closing parenthesis in next line when multi-line control structure
            if ($tokens[$i]['code'] === T_CLOSE_PARENTHESIS
                && $tokens[$i]['line'] > $tokens[$tokens[$i]['parenthesis_opener']]['line']
            ) {
                $prev = $phpcsFile->findPrevious(
                    Tokens::$emptyTokens
                        + [
                            T_CLOSE_SHORT_ARRAY => T_CLOSE_SHORT_ARRAY,
                            T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET,
                            T_CLOSE_PARENTHESIS => T_CLOSE_PARENTHESIS,
                        ],
                    $i - 1,
                    null,
                    true
                );

                $owner = $phpcsFile->findPrevious(
                    Tokens::$emptyTokens,
                    $tokens[$i]['parenthesis_opener'] - 1,
                    null,
                    true
                );
                if ($tokens[$prev]['line'] === $tokens[$i]['line']
                    && in_array($tokens[$owner]['code'], $this->functionToken, true)
                    && $this->hasContainNewLine(
                        $phpcsFile,
                        $tokens[$i]['parenthesis_opener'],
                        $tokens[$i]['parenthesis_closer']
                    )
                ) {
                    $error = 'Closing parenthesis must be in next line';
                    $fix = $phpcsFile->addFixableError($error, $i, 'ClosingParenthesis');

                    if ($fix) {
                        $phpcsFile->fixer->addNewlineBefore($i);
                    }
                }

                if (isset($tokens[$owner]['scope_condition'])) {
                    $scopeCondition = $tokens[$owner];
                    $prev = $i;
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
                        if ($tokens[$prev]['line'] <= $tokens[$i]['line'] - 1) {
                            $error = 'Invalid closing parenthesis position.';
                            $fix = $phpcsFile->addFixableError($error, $prev, 'InvalidClosingParenthesisPosition');

                            if ($fix) {
                                $phpcsFile->fixer->beginChangeset();
                                for ($j = $prev + 1; $j < $i; ++$j) {
                                    if ($tokens[$j]['code'] === T_WHITESPACE) {
                                        $phpcsFile->fixer->replaceToken($j, '');
                                    }
                                }
                                $phpcsFile->fixer->endChangeset();
                            }
                        } elseif ($tokens[$prev + 1]['code'] === T_WHITESPACE) {
                            $error = 'Unexpected whitespace before closing parenthesis.';
                            $fix = $phpcsFile->addFixableError(
                                $error,
                                $prev + 1,
                                'UnexpectedSpacesBeforeClosingParenthesis'
                            );

                            if ($fix) {
                                $phpcsFile->fixer->replaceToken($prev + 1, '');
                            }
                        }
                    }
                }
            }

            if ($tokens[$i]['code'] === T_DOUBLE_ARROW) {
                $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $i - 1, null, true);

                if ($tokens[$prev]['line'] === $tokens[$i]['line']) {
                    $next = $phpcsFile->findNext(Tokens::$emptyTokens, $i + 1, null, true);

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
                            $newEI = $this->indent;
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
                    // and not a control structure
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
                    if ($this->getControlStructurePtr($phpcsFile, $next) !== false) {
                        $addIndent = $expectedIndent + $extraIndent - $this->indent <= $previousIndent
                            && ! in_array($tokens[$next]['code'], Tokens::$booleanOperators, true);
                    } else {
                        $addIndent = $expectedIndent + $extraIndent <= $previousIndent;
                    }

                    if ($addIndent) {
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

                // no extra indent if there is no new line between open and close brackets
                if (! $this->hasContainNewLine($phpcsFile, $i, $xEnd)) {
                    continue;
                }

                // If open parenthesis belongs to control structure
                if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS
                    && isset($tokens[$i]['parenthesis_owner'])
                    && in_array($tokens[$tokens[$i]['parenthesis_owner']]['code'], $this->controlStructures, true)
                ) {
                    // search for first non-empty token in line,
                    // where is the closing parenthesis of the control structure
                    $firstOnLine = $phpcsFile->findFirstOnLine(Tokens::$emptyTokens, $xEnd, true);

                    $extraIndent += $this->indent;
                    if (isset($extras[$firstOnLine])) {
                        $extras[$firstOnLine] += $this->indent;
                    } else {
                        $extras[$firstOnLine] = $this->indent;
                    }

                    $controlStructure[$tokens[$i]['line']] = $tokens[$i]['parenthesis_closer'];

                    continue;
                }

                // If there is another open bracket in the current line,
                // and closing bracket is in the same line as closing bracket of the current token
                // (or there is no no line break between them)
                // skip the current token to count indent.
                $another = $i;
                $openTags = [T_OPEN_PARENTHESIS, T_OPEN_SHORT_ARRAY, T_OPEN_CURLY_BRACKET];
                while (($another = $phpcsFile->findNext($openTags, $another + 1))
                    && $tokens[$another]['line'] === $tokens[$i]['line']
                ) {
                    if (($tokens[$another]['code'] === T_OPEN_PARENTHESIS
                            && $tokens[$tokens[$another]['parenthesis_closer']]['line'] > $tokens[$another]['line']
                            && ! $this->hasContainNewLine($phpcsFile, $tokens[$another]['parenthesis_closer'], $xEnd))
                        || ($tokens[$another]['code'] === T_OPEN_SHORT_ARRAY
                            && $tokens[$tokens[$another]['bracket_closer']]['line'] > $tokens[$another]['line']
                            && ! $this->hasContainNewLine($phpcsFile, $tokens[$another]['bracket_closer'], $xEnd))
                        || ($tokens[$another]['code'] === T_OPEN_CURLY_BRACKET
                            && isset($tokens[$another]['scope_closer'])
                            && $tokens[$tokens[$another]['scope_closer']]['line'] > $tokens[$another]['line']
                            && ! $this->hasContainNewLine($phpcsFile, $tokens[$another]['scope_closer'], $xEnd))
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

                $ei1 = 0;
                if ($tokens[$first]['level'] === $tokens[$firstInNextLine]['level']
                    && $tokens[$firstInNextLine]['code'] !== T_CLOSE_CURLY_BRACKET
                ) {
                    $ei1 = $this->indent;
                    if (isset($extras[$xEnd])) {
                        $extras[$xEnd] += $ei1;
                    } else {
                        $extras[$xEnd] = $ei1;
                    }
                }

                $ei2 = 0;
                $next = $phpcsFile->findNext(Tokens::$emptyTokens, $i + 1, null, true);
                if ($tokens[$next]['line'] > $tokens[$i]['line']) {
                    // current line indent
                    $whitespace = $phpcsFile->findFirstOnLine([], $i, true);
                    if ($tokens[$whitespace]['code'] === T_WHITESPACE) {
                        $sum = strlen($tokens[$whitespace]['content'])
                            - $tokens[$first]['level'] * $this->indent
                            - $extraIndent;

                        if ($sum > 0) {
                            $ei2 = $sum;
                            if (isset($extras[$xEnd + 1])) {
                                $extras[$xEnd + 1] += $ei2;
                            } else {
                                $extras[$xEnd + 1] = $ei2;
                            }
                        }
                    }
                }

                $extraIndent += $ei1 + $ei2;
            }
        }

        return $phpcsFile->numTokens + 1;
    }

    /**
     * @todo: need name refactor and method description
     */
    private function fp(File $phpcsFile, int $ptr) : ?int
    {
        if ($this->alignObjectOperators) {
            $tokens = $phpcsFile->getTokens();

            while (--$ptr) {
                if ($tokens[$ptr]['code'] === T_CLOSE_PARENTHESIS) {
                    $ptr = $tokens[$ptr]['parenthesis_opener'];
                } elseif ($tokens[$ptr]['code'] === T_CLOSE_CURLY_BRACKET
                    || $tokens[$ptr]['code'] === T_CLOSE_SHORT_ARRAY
                    || $tokens[$ptr]['code'] === T_CLOSE_SQUARE_BRACKET
                ) {
                    $ptr = $tokens[$ptr]['bracket_opener'];
                } elseif ($tokens[$ptr]['code'] === T_OBJECT_OPERATOR) {
                    return $tokens[$ptr]['column'];
                } elseif (in_array(
                    $tokens[$ptr]['code'],
                    [T_SEMICOLON, T_OPEN_CURLY_BRACKET, T_OPEN_PARENTHESIS, T_OPEN_SHORT_ARRAY, T_OPEN_SQUARE_BRACKET],
                    true
                )) {
                    break;
                }
            }
        }

        return null;
    }

    /**
     * @todo: need name refactor and method description
     */
    private function np(File $phpcsFile, int $ptr) : ?int
    {
        $tokens = $phpcsFile->getTokens();

        while (++$ptr) {
            if ($tokens[$ptr]['code'] === T_OPEN_PARENTHESIS) {
                $ptr = $tokens[$ptr]['parenthesis_closer'];
            } elseif ($tokens[$ptr]['code'] === T_OPEN_CURLY_BRACKET) {
                $ptr = $tokens[$ptr]['bracket_closer'];
            } elseif (in_array(
                $tokens[$ptr]['code'],
                [T_OBJECT_OPERATOR, T_SEMICOLON, T_CLOSE_PARENTHESIS, T_CLOSE_SHORT_ARRAY],
                true
            )) {
                return $ptr;
            }
        }

        return null;
    }

    /**
     * Checks if there is another object operator
     * before $ptr token.
     */
    private function hasPrevObjectOperator(File $phpcsFile, int $ptr) : ?int
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

    /**
     * Checks if between $fromPtr and $toPtr is any new line
     * excluding scopes (arrays, closures, multiline function calls).
     */
    private function hasContainNewLine(File $phpcsFile, int $fromPtr, int $toPtr) : bool
    {
        $tokens = $phpcsFile->getTokens();

        for ($j = $fromPtr + 1; $j < $toPtr; ++$j) {
            switch ($tokens[$j]['code']) {
                case T_OPEN_PARENTHESIS:
                case T_ARRAY:
                    $j = $tokens[$j]['parenthesis_closer'];
                    continue 2;
                case T_OPEN_CURLY_BRACKET:
                    if (isset($tokens[$j]['scope_closer'])) {
                        $j = $tokens[$j]['scope_closer'];
                    }
                    continue 2;
                case T_OPEN_SHORT_ARRAY:
                    $j = $tokens[$j]['bracket_closer'];
                    continue 2;
                case T_WHITESPACE:
                    if (strpos($tokens[$j]['content'], $phpcsFile->eolChar) !== false) {
                        return true;
                    }
            }
        }

        return false;
    }

    /**
     * Checks if the $ptr token is inside control structure
     * and returns the control structure pointer;
     * otherwise returns boolean `false`.
     *
     * @return false|int
     */
    private function getControlStructurePtr(File $phpcsFile, int $ptr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$ptr]['nested_parenthesis'])) {
            foreach ($tokens[$ptr]['nested_parenthesis'] as $start => $end) {
                // find expression before
                $prev = $phpcsFile->findPrevious(
                    Tokens::$emptyTokens,
                    $start - 1,
                    null,
                    true
                );

                if (in_array($tokens[$prev]['code'], $this->controlStructures, true)) {
                    return $prev;
                }
            }
        }

        return false;
    }
}

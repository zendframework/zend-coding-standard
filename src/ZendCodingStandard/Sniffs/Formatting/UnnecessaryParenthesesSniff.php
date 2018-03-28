<?php

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use function in_array;

use const T_ANON_CLASS;
use const T_BITWISE_AND;
use const T_BITWISE_OR;
use const T_BITWISE_XOR;
use const T_BOOLEAN_NOT;
use const T_CLONE;
use const T_CLOSE_CURLY_BRACKET;
use const T_CLOSE_PARENTHESIS;
use const T_CLOSE_SHORT_ARRAY;
use const T_CLOSE_SQUARE_BRACKET;
use const T_CLOSE_TAG;
use const T_COALESCE;
use const T_COLON;
use const T_COMMA;
use const T_DOUBLE_ARROW;
use const T_ECHO;
use const T_EMPTY;
use const T_EVAL;
use const T_EXIT;
use const T_INCLUDE;
use const T_INCLUDE_ONCE;
use const T_INLINE_ELSE;
use const T_INLINE_THEN;
use const T_INSTANCEOF;
use const T_ISSET;
use const T_LIST;
use const T_OBJECT_OPERATOR;
use const T_OPEN_PARENTHESIS;
use const T_OPEN_TAG;
use const T_REQUIRE;
use const T_REQUIRE_ONCE;
use const T_RETURN;
use const T_SELF;
use const T_SEMICOLON;
use const T_STATIC;
use const T_STRING;
use const T_STRING_CONCAT;
use const T_UNSET;
use const T_USE;
use const T_VARIABLE;

class UnnecessaryParenthesesSniff implements Sniff
{
    /**
     * @var int[]
     */
    private $parenthesesAllowedTokens = [
        T_ANON_CLASS,
        T_CLOSE_CURLY_BRACKET,
        T_CLOSE_PARENTHESIS,
        T_CLOSE_SQUARE_BRACKET,
        T_EMPTY,
        T_EVAL,
        T_EXIT,
        T_ISSET,
        T_LIST,
        T_SELF,
        T_STATIC,
        T_STRING,
        T_UNSET,
        T_USE,
        T_VARIABLE,
    ];

    /**
     * @var int[]
     */
    private $endTokens = [
        T_INLINE_ELSE,
        T_INLINE_THEN,
        T_COLON,
        T_COMMA,
        T_DOUBLE_ARROW,
        T_SEMICOLON,
        T_CLOSE_PARENTHESIS,
        T_CLOSE_SQUARE_BRACKET,
        T_CLOSE_CURLY_BRACKET,
        T_CLOSE_SHORT_ARRAY,
        T_OPEN_TAG,
        T_CLOSE_TAG,
    ];

    /**
     * @var int[]
     */
    private $spaceTokens = [
        T_CLONE,
        T_ECHO,
        T_RETURN,
        T_INCLUDE,
        T_INCLUDE_ONCE,
        T_REQUIRE,
        T_REQUIRE_ONCE,
    ];

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_OPEN_PARENTHESIS];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['parenthesis_owner'])) {
            return;
        }

        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $stackPtr - 1, null, true);
        if (in_array($tokens[$prev]['code'], $this->parenthesesAllowedTokens, true)) {
            return;
        }

        $closePtr = $tokens[$stackPtr]['parenthesis_closer'];

        // Skip when method call on new instance i.e.: (new DateTime())->modify(...)
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, $closePtr + 1, null, true);
        if ($tokens[$next]['code'] === T_OBJECT_OPERATOR) {
            return;
        }

        $firstInside = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, $closePtr, true);
        $lastInside = $phpcsFile->findPrevious(Tokens::$emptyTokens, $closePtr - 1, $stackPtr + 1, true);

        if ($firstInside === $lastInside) {
            $this->error($phpcsFile, $stackPtr, $closePtr, 'SingleExpression');
            return;
        }

        if (! in_array($tokens[$prev]['code'], Tokens::$castTokens, true)) {
            $instanceOf = $phpcsFile->findNext(T_INSTANCEOF, $stackPtr + 1, $closePtr);
            if ($instanceOf !== false) {
                $op = $phpcsFile->findNext(Tokens::$booleanOperators, $stackPtr + 1, $closePtr);
                if ($op === false) {
                    $this->error($phpcsFile, $stackPtr, $closePtr, 'SingleInstanceOf');
                    return;
                }
            }
        }

        // Skip when operator before the parenthesis
        if (in_array($tokens[$prev]['code'], Tokens::$operators + Tokens::$booleanOperators, true)) {
            return;
        }

        // Skip when operator after the parenthesis
        if (in_array($tokens[$next]['code'], Tokens::$operators + Tokens::$booleanOperators, true)) {
            return;
        }

        // Check single expression casting
        if (in_array($tokens[$prev]['code'], Tokens::$castTokens, true)) {
            $op = $phpcsFile->findNext(
                Tokens::$assignmentTokens
                    + Tokens::$booleanOperators
                    + Tokens::$equalityTokens
                    + Tokens::$operators
                    + [
                        T_INLINE_ELSE => T_INLINE_ELSE,
                        T_INLINE_THEN => T_INLINE_THEN,
                        T_INSTANCEOF => T_INSTANCEOF,
                    ],
                $stackPtr + 1,
                $closePtr
            );

            if ($op === false) {
                $this->error($phpcsFile, $stackPtr, $closePtr, 'SingleCast');
            }
            return;
        }

        // Check single expression negation, concatenation or arithmetic operation
        $prevTokens = Tokens::$arithmeticTokens + [
            T_BOOLEAN_NOT => T_BOOLEAN_NOT,
            T_STRING_CONCAT => T_STRING_CONCAT,
        ];
        if (in_array($tokens[$prev]['code'], $prevTokens, true)) {
            $op = $phpcsFile->findNext(
                Tokens::$assignmentTokens
                    + Tokens::$booleanOperators
                    + Tokens::$equalityTokens
                    + Tokens::$operators
                    + [
                        T_INLINE_ELSE => T_INLINE_ELSE,
                        T_INLINE_THEN => T_INLINE_THEN,
                    ],
                $stackPtr + 1,
                $closePtr
            );

            if ($op === false) {
                $this->error($phpcsFile, $stackPtr, $closePtr, 'SingleNot');
            }
            return;
        }

        // Check single expression comparision
        if (in_array($tokens[$prev]['code'], Tokens::$equalityTokens, true)) {
            $op = $phpcsFile->findNext(
                Tokens::$arithmeticTokens
                    + Tokens::$assignmentTokens
                    + Tokens::$booleanOperators
                    + [
                        T_BITWISE_AND => T_BITWISE_AND,
                        T_BITWISE_OR => T_BITWISE_OR,
                        T_BITWISE_XOR => T_BITWISE_XOR,
                        T_COALESCE => T_COALESCE,
                        T_INLINE_ELSE => T_INLINE_ELSE,
                        T_INLINE_THEN => T_INLINE_THEN,
                    ],
                $stackPtr + 1,
                $closePtr
            );

            if ($op === false) {
                $this->error($phpcsFile, $stackPtr, $closePtr, 'SingleEquality');
            }
            return;
        }

        $endPtr = $phpcsFile->findNext($this->endTokens, $closePtr + 1);
        $lastPtr = $phpcsFile->findPrevious(Tokens::$emptyTokens, $endPtr - 1, null, true);

        if ($lastPtr === $closePtr) {
            // Nested ternary operator
            if (in_array($tokens[$prev]['code'], [T_INLINE_THEN, T_INLINE_ELSE], true)) {
                $op = $phpcsFile->findNext(
                    Tokens::$assignmentTokens
                        + Tokens::$booleanOperators
                        + [
                            T_COALESCE => T_COALESCE,
                            T_INLINE_ELSE => T_INLINE_ELSE,
                            T_INLINE_THEN => T_INLINE_THEN,
                        ],
                    $stackPtr + 1,
                    $closePtr
                );

                if ($op === false) {
                    $this->error($phpcsFile, $stackPtr, $closePtr, 'NestedTernary');
                }
                return;
            }

            $this->error($phpcsFile, $stackPtr, $closePtr, 'MultipleExpression');
        }
    }

    private function error(File $phpcsFile, int $openPtr, int $closePtr, string $errorCode) : void
    {
        $tokens = $phpcsFile->getTokens();

        $error = 'Parentheses around expression "%s" are redundant.';
        $data = [$phpcsFile->getTokensAsString($openPtr + 1, $closePtr - $openPtr - 1)];
        $fix = $phpcsFile->addFixableError($error, $openPtr, $errorCode, $data);

        if ($fix) {
            $phpcsFile->fixer->beginChangeset();
            if (in_array($tokens[$openPtr - 1]['code'], $this->spaceTokens, true)) {
                $phpcsFile->fixer->replaceToken($openPtr, ' ');
            } else {
                $phpcsFile->fixer->replaceToken($openPtr, '');
            }
            $phpcsFile->fixer->replaceToken($closePtr, '');
            $phpcsFile->fixer->endChangeset();
        }
    }
}

<?php
namespace ZendCodingStandard\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use function in_array;
use function str_repeat;
use function strtolower;
use function trim;

class ReturnTypeSniff implements Sniff
{
    /**
     * @var int
     */
    public $spacesBeforeColon = 0;

    /**
     * @var int
     */
    public $spacesAfterColon = 1;

    /**
     * @var int
     */
    public $spacesAfterNullable = 0;

    /**
     * @var string[]
     */
    private $simpleReturnTypes = [
        'void',
        'int',
        'float',
        'object',
        'string',
        'array',
        'iterable',
        'callable',
        'parent',
        'self',
        'bool',
    ];

    /**
     * @inheritDoc
     */
    public function register()
    {
        return [T_RETURN_TYPE];
    }

    /**
     * @inheritDoc
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $this->spacesBeforeColon = (int) $this->spacesBeforeColon;
        $this->spacesAfterColon = (int) $this->spacesAfterColon;
        $this->spacesAfterNullable = (int) $this->spacesAfterNullable;
        $tokens = $phpcsFile->getTokens();

        $colon = $phpcsFile->findPrevious(T_COLON, $stackPtr - 1);

        $expected = str_repeat(' ', $this->spacesBeforeColon);
        // Token before colon does not match configured number of spaces.
        if (($this->spacesBeforeColon === 0
                && $tokens[$colon - 1]['code'] === T_WHITESPACE)
            || ($this->spacesBeforeColon > 0
                && ($tokens[$colon - 1]['code'] !== T_WHITESPACE
                    || $tokens[$colon - 1]['content'] !== $expected))
        ) {
            $error = 'There must be exactly %d space(s) between the closing parenthesis and the colon'
                . ' when declaring a return type for a function';
            $data = [$this->spacesBeforeColon];
            $fix = $phpcsFile->addFixableError($error, $colon - 1, 'SpaceBeforeColon', $data);

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                if ($tokens[$colon - 1]['code'] === T_WHITESPACE) {
                    $phpcsFile->fixer->replaceToken($colon - 1, $expected);
                    if (isset($tokens[$colon - 2]) && $tokens[$colon - 2]['code'] === T_WHITESPACE) {
                        $phpcsFile->fixer->replaceToken($colon - 2, '');
                    }
                } else {
                    $phpcsFile->fixer->addContentBefore($colon, $expected);
                }
                $phpcsFile->fixer->endChangeset();
            }
        }

        $expected = str_repeat(' ', $this->spacesAfterColon);
        // Token after colon does not match configured number of spaces.
        if (($this->spacesAfterColon === 0
                && $tokens[$colon + 1]['code'] === T_WHITESPACE)
            || ($this->spacesAfterColon > 0
                && ($tokens[$colon + 1]['code'] !== T_WHITESPACE
                    || $tokens[$colon + 1]['content'] !== $expected))
        ) {
            $error = 'There must be exactly %d space(s) between the colon and return type'
                . ' when declaring a return type for a function';
            $data = [$this->spacesAfterColon];
            $fix = $phpcsFile->addFixableError($error, $colon, 'NoSpaceAfterColon', $data);

            if ($fix) {
                if ($tokens[$colon + 1]['code'] === T_WHITESPACE) {
                    $phpcsFile->fixer->replaceToken($colon + 1, $expected);
                } else {
                    $phpcsFile->fixer->addContent($colon, $expected);
                }
            }
        }

        $nullable = $phpcsFile->findNext(T_NULLABLE, $colon + 1, $stackPtr);
        if ($nullable) {
            $expected = str_repeat(' ', $this->spacesAfterNullable);
            // Token after nullable does not match configured number of spaces.
            if (($this->spacesAfterNullable === 0
                    && $tokens[$nullable + 1]['code'] === T_WHITESPACE)
                || ($this->spacesAfterNullable > 0
                    && ($tokens[$nullable + 1]['code'] !== T_WHITESPACE
                        || $tokens[$nullable + 1]['content'] !== $expected))
            ) {
                $error = 'There must be exactly %d space(s) between the nullable operator and return type'
                    . ' when declaring a return type for a function';
                $data = [$this->spacesAfterNullable];
                $fix = $phpcsFile->addFixableError($error, $nullable + 1, 'SpaceAfterNullable', $data);

                if ($fix) {
                    if ($tokens[$nullable + 1]['code'] === T_WHITESPACE) {
                        $phpcsFile->fixer->replaceToken($nullable + 1, $expected);
                    } else {
                        $phpcsFile->fixer->addContent($nullable, $expected);
                    }
                }
            }
        }

        $first = $phpcsFile->findNext(Tokens::$emptyTokens, ($nullable ?: $colon) + 1, null, true);
        $end = $phpcsFile->findNext([T_SEMICOLON, T_OPEN_CURLY_BRACKET], $stackPtr + 1);
        $last = $phpcsFile->findPrevious(Tokens::$emptyTokens, $end - 1, null, true);

        $invalid = $phpcsFile->findNext([T_STRING, T_NS_SEPARATOR, T_RETURN_TYPE], $first, $last + 1, true);
        if ($invalid) {
            $error = 'Return type declaration contains invalid token %s';
            $data = [$tokens[$invalid]['type']];
            $fix = $phpcsFile->addFixableError($error, $invalid, 'InvalidToken', $data);

            if ($fix) {
                $phpcsFile->fixer->replaceToken($invalid, '');
            }

            return;
        }

        $returnType = trim($phpcsFile->getTokensAsString($first, $last - $first + 1));

        if ($first === $last
            && in_array(strtolower($returnType), $this->simpleReturnTypes, true)
            && ! in_array($returnType, $this->simpleReturnTypes, true)
        ) {
            $error = 'Simple return type must be lowercase. Found "%s", expected "%s"';
            $data = [
                $returnType,
                strtolower($returnType),
            ];
            $fix = $phpcsFile->addFixableError($error, $first, 'LowerCaseSimpleType', $data);

            if ($fix) {
                $phpcsFile->fixer->replaceToken($stackPtr, strtolower($returnType));
            }
        }
    }
}

<?php
namespace ZendCodingStandard\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use function in_array;
use function str_repeat;
use function strtolower;
use function trim;

use const T_CLOSE_PARENTHESIS;
use const T_COLON;
use const T_NS_SEPARATOR;
use const T_NULLABLE;
use const T_OPEN_CURLY_BRACKET;
use const T_RETURN_TYPE;
use const T_SEMICOLON;
use const T_STRING;
use const T_WHITESPACE;

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

        // Check if between the closing parenthesis and return type are only allowed tokens.
        $parenthesisCloser = $phpcsFile->findPrevious(
            [
                T_COLON,
                T_NS_SEPARATOR,
                T_NULLABLE,
                T_STRING,
                T_WHITESPACE,
            ],
            $stackPtr - 1,
            null,
            true
        );
        if ($tokens[$parenthesisCloser]['code'] !== T_CLOSE_PARENTHESIS) {
            $error = 'Return type declaration contains invalid token %s';
            $data = [$tokens[$parenthesisCloser]['type']];
            $phpcsFile->addError($error, $parenthesisCloser, 'InvalidToken', $data);

            return;
        }

        $colon = $phpcsFile->findPrevious(T_COLON, $stackPtr - 1);
        $nullable = $phpcsFile->findNext(T_NULLABLE, $colon + 1, $stackPtr);

        $this->checkSpacesBeforeColon($phpcsFile, $colon);
        $this->checkSpacesAfterColon($phpcsFile, $colon);
        if ($nullable) {
            $this->checkSpacesAfterNullable($phpcsFile, $nullable);
        }

        $first = $phpcsFile->findNext(Tokens::$emptyTokens, ($nullable ?: $colon) + 1, null, true);
        $end = $phpcsFile->findNext([T_SEMICOLON, T_OPEN_CURLY_BRACKET], $stackPtr + 1);
        $last = $phpcsFile->findPrevious(Tokens::$emptyTokens, $end - 1, null, true);

        $space = $phpcsFile->findNext(T_WHITESPACE, $first, $last + 1);
        if ($space) {
            $error = 'Return type declaration contains invalid token %s';
            $data = [$tokens[$space]['type']];
            $fix = $phpcsFile->addFixableError($error, $space, 'SpaceInReturnType', $data);

            if ($fix) {
                $phpcsFile->fixer->replaceToken($space, '');
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

    /**
     * Check if token before colon match configured number of spaces.
     *
     * @param File $phpcsFile
     * @param int $colon
     * @return void
     */
    private function checkSpacesBeforeColon(File $phpcsFile, $colon)
    {
        $tokens = $phpcsFile->getTokens();

        // The whitespace before colon is not expected and it is not present.
        if ($this->spacesBeforeColon === 0
            && $tokens[$colon - 1]['code'] !== T_WHITESPACE
        ) {
            return;
        }

        $expected = str_repeat(' ', $this->spacesBeforeColon);

        // Previous token contains expected number of spaces,
        // and before whitespace there is close parenthesis token.
        if ($this->spacesBeforeColon > 0
            && $tokens[$colon - 1]['content'] === $expected
            && $tokens[$colon - 2]['code'] === T_CLOSE_PARENTHESIS
        ) {
            return;
        }

        $error = 'There must be exactly %d space(s) between the closing parenthesis and the colon'
            . ' when declaring a return type for a function';
        $data = [$this->spacesBeforeColon];
        $fix = $phpcsFile->addFixableError($error, $colon - 1, 'SpacesBeforeColon', $data);

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

    /**
     * Check if token after colon match configured number of spaces.
     *
     * @param File $phpcsFile
     * @param int $colon
     * @return void
     */
    private function checkSpacesAfterColon(File $phpcsFile, $colon)
    {
        $tokens = $phpcsFile->getTokens();

        // The whitespace after colon is not expected and it is not present.
        if ($this->spacesAfterColon === 0
            && $tokens[$colon + 1]['code'] !== T_WHITESPACE
        ) {
            return;
        }

        $expected = str_repeat(' ', $this->spacesAfterColon);

        // Next token contains expected number of spaces.
        if ($this->spacesAfterColon > 0
            && $tokens[$colon + 1]['content'] === $expected
        ) {
            return;
        }

        $error = 'There must be exactly %d space(s) between the colon and return type'
            . ' when declaring a return type for a function';
        $data = [$this->spacesAfterColon];
        $fix = $phpcsFile->addFixableError($error, $colon, 'SpacesAfterColon', $data);

        if ($fix) {
            if ($tokens[$colon + 1]['code'] === T_WHITESPACE) {
                $phpcsFile->fixer->replaceToken($colon + 1, $expected);
            } else {
                $phpcsFile->fixer->addContent($colon, $expected);
            }
        }
    }

    /**
     * Checks if token after nullable operator match configured number of spaces.
     *
     * @param File $phpcsFile
     * @param int $nullable
     * @return void
     */
    private function checkSpacesAfterNullable(File $phpcsFile, $nullable)
    {
        $tokens = $phpcsFile->getTokens();

        // The whitespace after nullable operator is not expected and it is not present.
        if ($this->spacesAfterNullable === 0
            && $tokens[$nullable + 1]['code'] !== T_WHITESPACE
        ) {
            return;
        }

        $expected = str_repeat(' ', $this->spacesAfterNullable);

        // Next token contains expected number of spaces.
        if ($this->spacesAfterNullable > 0
            && $tokens[$nullable + 1]['content'] === $expected
        ) {
            return;
        }

        $error = 'There must be exactly %d space(s) between the nullable operator and return type'
            . ' when declaring a return type for a function';
        $data = [$this->spacesAfterNullable];
        $fix = $phpcsFile->addFixableError($error, $nullable + 1, 'SpacesAfterNullable', $data);

        if ($fix) {
            if ($tokens[$nullable + 1]['code'] === T_WHITESPACE) {
                $phpcsFile->fixer->replaceToken($nullable + 1, $expected);
            } else {
                $phpcsFile->fixer->addContent($nullable, $expected);
            }
        }
    }
}

<?php
namespace ZendCodingStandard\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use function in_array;
use function strtolower;
use function trim;

class ReturnTypeSniff implements Sniff
{
    /**
     * @var string[]
     */
    private $simpleReturnTypes = [
        'void',
        'int',
        'float',
        'double',
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
        $tokens = $phpcsFile->getTokens();

        $colon = $phpcsFile->findPrevious(T_COLON, $stackPtr - 1);

        // No space before colon.
        if ($tokens[$colon - 1]['code'] !== T_CLOSE_PARENTHESIS) {
            $error = 'There must be no space before colon.';
            $fix = $phpcsFile->addFixableError($error, $colon - 1, 'SpaceBeforeColon');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $token = $colon - 1;
                do {
                    $phpcsFile->fixer->replaceToken($token, '');

                    --$token;
                } while ($tokens[$token]['code'] !== T_CLOSE_PARENTHESIS);
                $phpcsFile->fixer->endChangeset();
            }
        }

        // Only one space after colon.
        if ($tokens[$colon + 1]['code'] !== T_WHITESPACE) {
            $error = 'There must be one space after colon and before return type declaration.';
            $fix = $phpcsFile->addFixableError($error, $colon, 'NoSpaceAfterColon');

            if ($fix) {
                $phpcsFile->fixer->addContent($colon, ' ');
            }
        } elseif ($tokens[$colon + 1]['content'] !== ' ') {
            $error = 'There must be only one space after colon and before return type declaration.';
            $fix = $phpcsFile->addFixableError($error, $colon + 1, 'TooManySpacesAfterColon');

            if ($fix) {
                $phpcsFile->fixer->replaceToken($colon + 1, ' ');
            }
        }

        $nullable = $phpcsFile->findNext(T_NULLABLE, $colon + 1, $stackPtr);
        if ($nullable) {
            // Check if there is space after nullable operator.
            if ($tokens[$nullable + 1]['code'] === T_WHITESPACE) {
                $error = 'Space is not not allowed after nullable operator.';
                $fix = $phpcsFile->addFixableError($error, $nullable + 1, 'SpaceAfterNullable');

                if ($fix) {
                    $phpcsFile->fixer->replaceToken($nullable + 1, '');
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

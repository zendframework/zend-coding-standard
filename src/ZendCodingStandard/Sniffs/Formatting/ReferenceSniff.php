<?php
namespace ZendCodingStandard\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use function in_array;

use const T_BITWISE_AND;
use const T_COMMA;
use const T_OPEN_PARENTHESIS;
use const T_OPEN_SHORT_ARRAY;
use const T_WHITESPACE;

class ReferenceSniff implements Sniff
{
    /**
     * @inheritDoc
     */
    public function register()
    {
        return [T_BITWISE_AND];
    }

    /**
     * @inheritDoc
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $stackPtr - 1, null, true);

        $tokenCodes = Tokens::$assignmentTokens + [
            T_COMMA => T_COMMA,
            T_OPEN_PARENTHESIS => T_OPEN_PARENTHESIS,
            T_OPEN_SHORT_ARRAY => T_OPEN_SHORT_ARRAY,
        ];
        if (! in_array($tokens[$prev]['code'], $tokenCodes, true)) {
            return;
        }

        // One space before &
        if ($tokens[$prev]['line'] === $tokens[$stackPtr]['line']
            && ! in_array($tokens[$stackPtr - 1]['code'], [T_WHITESPACE, T_OPEN_PARENTHESIS, T_OPEN_SHORT_ARRAY], true)
        ) {
            $error = 'Missing space before reference character';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'MissingSpace');

            if ($fix) {
                $phpcsFile->fixer->addContentBefore($stackPtr, ' ');
            }
        }

        // No space after &
        if ($tokens[$stackPtr + 1]['code'] === T_WHITESPACE) {
            $error = 'Unexpected space after reference character';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'UnexpectedSpace');

            if ($fix) {
                $phpcsFile->fixer->replaceToken($stackPtr + 1, '');
            }
        }
    }
}

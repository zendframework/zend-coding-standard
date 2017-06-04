<?php
/**
 * Copied from:
 * @see https://github.com/dereuromark/codesniffer-standards/blob/master/CakePHP/Sniffs/PHP/TypeCastingSniff.php
 *
 * Changes:
 * - disallow (unset) cast
 * - omit white chars in casting
 */

namespace ZendCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class TypeCastingSniff implements Sniff
{
    /**
     * @inheritDoc
     */
    public function register()
    {
        return array_merge(Tokens::$castTokens, [T_BOOLEAN_NOT]);
    }

    /**
     * @inheritDoc
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Process !! casts
        if ($tokens[$stackPtr]['code'] === T_BOOLEAN_NOT) {
            $nextToken = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);
            if ($tokens[$nextToken]['code'] !== T_BOOLEAN_NOT) {
                return;
            }
            $error = 'Usage of !! cast is not allowed. Please use (bool) to cast.';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'NotAllowed');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->replaceToken($stackPtr, '(bool)');
                $phpcsFile->fixer->replaceToken($nextToken, '');
                $phpcsFile->fixer->endChangeset();
            }

            return;
        }

        if ($tokens[$stackPtr]['code'] === T_UNSET_CAST) {
            $phpcsFile->addError('(unset) casting is not allowed.', $stackPtr, 'UnsetCast');
            return;
        }

        // Only allow short forms if both short and long forms are possible
        $matching = [
            '(boolean)' => '(bool)',
            '(integer)' => '(int)',
        ];
        $content = $tokens[$stackPtr]['content'];
        $key = preg_replace('/\s/', '', strtolower($content));
        if (isset($matching[$key]) || $content !== $key) {
            $error = 'Please use %s instead of %s.';
            $expected = isset($matching[$key]) ? $matching[$key] : $key;
            $data = [
                $expected,
                $content,
            ];
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'NotAllowed', $data);

            if ($fix) {
                $phpcsFile->fixer->replaceToken($stackPtr, $expected);
            }

            return;
        }
    }
}

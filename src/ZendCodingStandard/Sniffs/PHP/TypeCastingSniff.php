<?php

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use function preg_replace;
use function strtolower;

use const T_BOOLEAN_NOT;
use const T_UNSET_CAST;
use const T_WHITESPACE;

class TypeCastingSniff implements Sniff
{
    /**
     * @var array
     */
    private $castMap = [
        '(boolean)' => '(bool)',
        '(integer)' => '(int)',
        '(real)'    => '(float)',
        '(double)'  => '(float)',
    ];

    /**
     * @return int[]
     */
    public function register() : array
    {
        return Tokens::$castTokens
            + [T_BOOLEAN_NOT => T_BOOLEAN_NOT];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_BOOLEAN_NOT) {
            $nextToken = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);
            if (! $nextToken || $tokens[$nextToken]['code'] !== T_BOOLEAN_NOT) {
                return;
            }
            $error = 'Double negation casting is not allowed. Please use (bool) instead.';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'DoubleNot');

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

        $content = $tokens[$stackPtr]['content'];
        $expected = preg_replace('/\s/', '', strtolower($content));
        if ($content !== $expected || isset($this->castMap[$expected])) {
            $error = 'Invalid casting used. Expected %s, found %s';
            $expected = $this->castMap[$expected] ?? $expected;
            $data = [
                $expected,
                $content,
            ];
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Invalid', $data);

            if ($fix) {
                $phpcsFile->fixer->replaceToken($stackPtr, $expected);
            }

            return;
        }
    }
}

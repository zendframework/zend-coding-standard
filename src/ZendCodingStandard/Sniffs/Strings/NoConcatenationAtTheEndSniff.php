<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYRIGHT.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\Strings;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use const T_STRING_CONCAT;
use const T_WHITESPACE;

class NoConcatenationAtTheEndSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_STRING_CONCAT];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $next = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);

        if ($tokens[$stackPtr]['line'] === $tokens[$next]['line']) {
            return;
        }

        $error = 'String concatenation character is not allowed at the end of the line.';
        $fix = $phpcsFile->addFixableError($error, $stackPtr, 'ConcatenationAtTheEnd');

        if ($fix) {
            $phpcsFile->fixer->beginChangeset();
            if ($tokens[$stackPtr - 1]['code'] === T_WHITESPACE) {
                $phpcsFile->fixer->replaceToken($stackPtr - 1, '');
            }
            $phpcsFile->fixer->replaceToken($stackPtr, '');
            $phpcsFile->fixer->addContentBefore($next, '. ');
            $phpcsFile->fixer->endChangeset();
        }
    }
}

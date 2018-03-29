<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYRIGHT.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use function in_array;
use function strtolower;

use const T_ANON_CLASS;
use const T_CLOSE_CURLY_BRACKET;
use const T_CLOSURE;
use const T_SEMICOLON;

class RedundantSemicolonSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_CLOSE_CURLY_BRACKET];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (! isset($tokens[$stackPtr]['scope_condition'])) {
            return;
        }

        $scopeCondition = $tokens[$stackPtr]['scope_condition'];
        if (in_array($tokens[$scopeCondition]['code'], [T_ANON_CLASS, T_CLOSURE], true)) {
            return;
        }

        $nextNonEmpty = $phpcsFile->findNext(
            Tokens::$emptyTokens,
            $stackPtr + 1,
            null,
            true
        );

        if ($tokens[$nextNonEmpty]['code'] === T_SEMICOLON) {
            $error = 'Redundant semicolon after control structure "%s".';
            $data = [strtolower($tokens[$scopeCondition]['content'])];
            $fix = $phpcsFile->addFixableError($error, $nextNonEmpty, 'SemicolonFound', $data);

            if ($fix) {
                $phpcsFile->fixer->replaceToken($nextNonEmpty, '');
            }
        }
    }
}

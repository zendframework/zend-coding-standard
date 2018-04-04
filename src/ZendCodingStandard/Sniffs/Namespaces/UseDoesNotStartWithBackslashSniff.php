<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYRIGHT.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use ZendCodingStandard\CodingStandard;

use function strtolower;

use const T_NS_SEPARATOR;
use const T_STRING;
use const T_USE;
use const T_WHITESPACE;

class UseDoesNotStartWithBackslashSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_USE];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if (! CodingStandard::isGlobalUse($phpcsFile, $stackPtr)) {
            return;
        }

        $tokens = $phpcsFile->getTokens();
        $classPtr = $phpcsFile->findNext(
            Tokens::$emptyTokens,
            $stackPtr + 1,
            null,
            true
        );

        $lowerContent = strtolower($tokens[$classPtr]['content']);
        if ($lowerContent === 'function' || $lowerContent === 'const') {
            $classPtr = $phpcsFile->findNext(
                Tokens::$emptyTokens,
                $classPtr + 1,
                null,
                true
            );
        }

        if ($tokens[$classPtr]['code'] === T_NS_SEPARATOR
            || ($tokens[$classPtr]['code'] === T_STRING
                && $tokens[$classPtr]['content'] === '\\')
        ) {
            $error = 'Use statement cannot start with a backslash';
            $fix = $phpcsFile->addFixableError($error, $classPtr, 'BackslashAtStart');

            if ($fix) {
                if ($tokens[$classPtr - 1]['code'] !== T_WHITESPACE) {
                    $phpcsFile->fixer->replaceToken($classPtr, ' ');
                } else {
                    $phpcsFile->fixer->replaceToken($classPtr, '');
                }
            }
        }
    }
}

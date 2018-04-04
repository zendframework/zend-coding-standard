<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYRIGHT.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use function preg_match;
use function preg_replace;

use const T_OPEN_TAG;

class PhpcsAnnotationSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_OPEN_TAG];
    }

    /**
     * @param int $stackPtr
     * @return int
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $ignoredLines = $phpcsFile->tokenizer->ignoredLines;
        $phpcsFile->tokenizer->ignoredLines = [];

        $next = $stackPtr;
        while ($next = $phpcsFile->findNext(Tokens::$phpcsCommentTokens, $next + 1)) {
            $this->overrideToken($phpcsFile, $next);

            if ($tokens[$next - 1]['content'] !== '@'
                && ! preg_match('/@phpcs:/i', $tokens[$next]['content'])
            ) {
                $error = 'Missing @ before phpcs annotation';
                $fix = $phpcsFile->addFixableError($error, $next, 'MissingAt');

                if ($fix) {
                    $content = preg_replace('/phpcs:/i', '@\\0', $tokens[$next]['content']);
                    $phpcsFile->fixer->replaceToken($next, $content);
                }
            }
        }

        $phpcsFile->tokenizer->ignoredLines = $ignoredLines;

        return $phpcsFile->numTokens + 1;
    }

    private function overrideToken(File $phpcsFile, int $stackPtr)
    {
        $clear = function () use ($stackPtr) {
            $this->tokens[$stackPtr]['content'] = 'ZF-CS';
        };

        $clear->bindTo($phpcsFile, File::class)();
    }
}

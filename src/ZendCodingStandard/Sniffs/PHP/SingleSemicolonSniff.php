<?php

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use const T_SEMICOLON;

class SingleSemicolonSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_SEMICOLON];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true);

        if ($next && $tokens[$next]['code'] === T_SEMICOLON) {
            $error = 'Redundant semicolon';
            $fix = $phpcsFile->addFixableError($error, $next, 'Semicolon');

            if ($fix) {
                $phpcsFile->fixer->replaceToken($next, '');
            }
        }
    }
}

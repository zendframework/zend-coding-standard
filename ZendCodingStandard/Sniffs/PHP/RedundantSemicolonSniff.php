<?php
namespace ZendCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;
use PHP_CodeSniffer_Tokens;

class RedundantSemicolonSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * @return int[]
     */
    public function register()
    {
        return [T_CLOSE_CURLY_BRACKET];
    }

    /**
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
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
            PHP_CodeSniffer_Tokens::$emptyTokens,
            $stackPtr + 1,
            null,
            true
        );

        if ($tokens[$nextNonEmpty]['code'] === T_SEMICOLON) {
            $error = 'Redundant semicolon after control structure "%s".';
            $data = [
                strtolower($tokens[$scopeCondition]['content']),
            ];
            $fix = $phpcsFile->addFixableError($error, $nextNonEmpty, '', $data);

            if ($fix) {
                $phpcsFile->fixer->replaceToken($nextNonEmpty, '');
            }
        }
    }
}

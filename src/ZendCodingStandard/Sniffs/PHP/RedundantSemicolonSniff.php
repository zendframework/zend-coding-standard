<?php
namespace ZendCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class RedundantSemicolonSniff implements Sniff
{
    /**
     * @inheritDoc
     */
    public function register()
    {
        return [T_CLOSE_CURLY_BRACKET];
    }

    /**
     * @inheritDoc
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

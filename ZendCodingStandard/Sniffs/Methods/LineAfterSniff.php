<?php
/**
 * Check if between methods of the class is exactly one blank line.
 */
namespace ZendCodingStandard\Sniffs\Methods;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Standards_AbstractScopeSniff;

class LineAfterSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{
    public function __construct()
    {
        parent::__construct([T_CLASS, T_INTERFACE], [T_FUNCTION]);
    }

    protected function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope)
    {
        $tokens = $phpcsFile->getTokens();

        $prev = $phpcsFile->findPrevious(
            array_merge(\PHP_CodeSniffer_Tokens::$methodPrefixes, [T_WHITESPACE]),
            $stackPtr - 1,
            null,
            true
        );

        $visibility = $phpcsFile->findPrevious(
            [T_PUBLIC, T_PROTECTED, T_PRIVATE],
            $stackPtr - 1,
            $prev
        );

        // Skip methods without visibility.
        if (! $visibility) {
            return;
        }

        // Skip methods without body.
        if (! isset($tokens[$stackPtr]['scope_closer'])) {
            return;
        }

        $scopeCloser = $tokens[$stackPtr]['scope_closer'];

        $contentAfter  = $phpcsFile->findNext(T_WHITESPACE, $scopeCloser + 1, null, true);
        if ($contentAfter !== false
            && $tokens[$contentAfter]['line'] - $tokens[$scopeCloser]['line'] !== 2
            && $tokens[$contentAfter]['code'] !== T_CLOSE_CURLY_BRACKET
        ) {
            $error = 'Expected 1 blank line after method; %d found';
            $found = max($tokens[$contentAfter]['line'] - $tokens[$scopeCloser]['line'] - 1, 0);
            $fix = $phpcsFile->addFixableError($error, $scopeCloser, '', [$found]);

            if ($fix) {
                if ($found) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = $scopeCloser + 1; $i < $contentAfter - 1; $i++) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }
                    $phpcsFile->fixer->endChangeset();
                } else {
                    $phpcsFile->fixer->addNewline($scopeCloser);
                }
            }
        }
    }
}

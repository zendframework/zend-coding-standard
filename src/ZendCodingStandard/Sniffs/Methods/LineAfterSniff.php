<?php
namespace ZendCodingStandard\Sniffs\Methods;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractScopeSniff;

class LineAfterSniff extends AbstractScopeSniff
{
    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct([T_CLASS, T_INTERFACE, T_TRAIT, T_ANON_CLASS], [T_FUNCTION]);
    }

    /**
     * @inheritDoc
     */
    protected function processTokenWithinScope(File $phpcsFile, $stackPtr, $currScope)
    {
        $tokens = $phpcsFile->getTokens();

        // Methods with body.
        if (isset($tokens[$stackPtr]['scope_closer'])) {
            $closer = $tokens[$stackPtr]['scope_closer'];
        } else {
            $closer = $phpcsFile->findNext(T_SEMICOLON, $tokens[$stackPtr]['parenthesis_closer'] + 1);
        }

        $contentAfter  = $phpcsFile->findNext(T_WHITESPACE, $closer + 1, null, true);
        if ($contentAfter !== false
            && $tokens[$contentAfter]['line'] - $tokens[$closer]['line'] !== 2
            && $tokens[$contentAfter]['code'] !== T_CLOSE_CURLY_BRACKET
        ) {
            $error = 'Expected 1 blank line after method; %d found';
            $found = max($tokens[$contentAfter]['line'] - $tokens[$closer]['line'] - 1, 0);
            $fix = $phpcsFile->addFixableError($error, $closer, '', [$found]);

            if ($fix) {
                if ($found) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = $closer + 1; $i < $contentAfter - 1; $i++) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }
                    $phpcsFile->fixer->endChangeset();
                } else {
                    $phpcsFile->fixer->addNewline($closer);
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function processTokenOutsideScope(File $phpcsFile, $stackPtr)
    {
    }
}

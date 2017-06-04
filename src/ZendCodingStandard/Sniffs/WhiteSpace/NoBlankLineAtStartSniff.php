<?php
namespace ZendCodingStandard\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class NoBlankLineAtStartSniff implements Sniff
{
    /**
     * @inheritDoc
     */
    public function register()
    {
        return [
            T_CLASS,
            T_CLOSURE,
            T_FUNCTION,
            T_INTERFACE,
            T_TRAIT,
        ];
    }

    /**
     * @inheritDoc
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $token = $tokens[$stackPtr];

        // Skip function without body.
        if (! isset($token['scope_opener'])) {
            return;
        }

        $scopeOpener = $tokens[$stackPtr]['scope_opener'];
        $firstContent = $phpcsFile->findNext(T_WHITESPACE, $scopeOpener + 1, null, true);

        if ($tokens[$firstContent]['line'] > $tokens[$scopeOpener]['line'] + 1) {
            $error = sprintf('Blank line found at start of %s', $token['content']);
            $fix = $phpcsFile->addFixableError($error, $scopeOpener + 1, '');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $i = $scopeOpener + 1;
                while ($tokens[$i]['line'] !== $tokens[$firstContent]['line']) {
                    $phpcsFile->fixer->replaceToken($i, '');
                    ++$i;
                }
                $phpcsFile->fixer->addNewline($scopeOpener);
                $phpcsFile->fixer->endChangeset();
            }
        }
    }
}

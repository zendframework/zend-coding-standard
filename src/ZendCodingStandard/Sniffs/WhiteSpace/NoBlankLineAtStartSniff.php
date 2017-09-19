<?php
namespace ZendCodingStandard\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use const T_CLASS;
use const T_CLOSURE;
use const T_FUNCTION;
use const T_INTERFACE;
use const T_TRAIT;
use const T_WHITESPACE;

class NoBlankLineAtStartSniff implements Sniff
{
    /**
     * @return int[]
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
     * @param int $stackPtr
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
            $error = 'Blank line found at start of %s';
            $data = [$token['content']];
            $fix = $phpcsFile->addFixableError($error, $scopeOpener + 1, 'BlankLine', $data);

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

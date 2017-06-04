<?php
namespace ZendCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class InstantiatingParenthesisSniff implements Sniff
{
    /**
     * @inheritDoc
     */
    public function register()
    {
        return [T_NEW];
    }

    /**
     * @inheritDoc
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $end = $phpcsFile->findNext(
            array_merge(Tokens::$emptyTokens, [
                T_ANON_CLASS,
                T_NS_SEPARATOR,
                T_SELF,
                T_STATIC,
                T_STRING,
                T_VARIABLE,
            ]),
            $stackPtr + 1,
            null,
            true
        );

        if ($tokens[$end]['code'] !== T_OPEN_PARENTHESIS) {
            $last = $phpcsFile->findPrevious(
                Tokens::$emptyTokens,
                $end - 1,
                $stackPtr + 1,
                true
            );

            $error = 'Missing parenthesis on instantiating a new class.';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, '');

            if ($fix) {
                $phpcsFile->fixer->addContent($last, '()');
            }
        }
    }
}

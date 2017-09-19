<?php
namespace ZendCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use const T_CLOSE_CURLY_BRACKET;
use const T_COMMENT;
use const T_WHITESPACE;

class NoInlineCommentAfterCurlyCloseSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register()
    {
        return [T_CLOSE_CURLY_BRACKET];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $next = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);
        if (! $next) {
            return;
        }

        if ($tokens[$next]['code'] !== T_COMMENT) {
            return;
        }

        if ($tokens[$next]['line'] > $tokens[$stackPtr]['line']) {
            return;
        }

        $error = 'Inline comment is not allowed after closing curly bracket.';
        $phpcsFile->addError($error, $next, 'NotAllowed');
    }
}

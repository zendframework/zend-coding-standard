<?php
namespace ZendCodingStandard\Sniffs\Operators;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class TernaryOperatorSniff implements Sniff
{
    /**
     * @inheritDoc
     */
    public function register()
    {
        return [
            T_INLINE_ELSE,
            T_INLINE_THEN,
        ];
    }

    /**
     * @inheritDoc
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $next = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true);
        if ($tokens[$next]['line'] > $tokens[$stackPtr]['line']) {
            $error = 'Invalid position of ternary operator "%s"';
            $data = [$tokens[$stackPtr]['content']];
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Invalid', $data);

            if ($fix) {
                $isShortTernary = $tokens[$stackPtr]['code'] === T_INLINE_THEN
                    && $tokens[$next]['code'] === T_INLINE_ELSE;

                $phpcsFile->fixer->beginChangeset();
                if ($tokens[$stackPtr - 1]['code'] === T_WHITESPACE) {
                    $phpcsFile->fixer->replaceToken($stackPtr - 1, '');
                }
                $phpcsFile->fixer->replaceToken($stackPtr, '');
                if ($isShortTernary) {
                    if ($tokens[$stackPtr + 1]['code'] === T_WHITESPACE) {
                        $phpcsFile->fixer->replaceToken($stackPtr + 1, '');
                    }
                    $phpcsFile->fixer->addContentBefore($next, $tokens[$stackPtr]['content']);
                } else {
                    $phpcsFile->fixer->addContentBefore($next, $tokens[$stackPtr]['content'] . ' ');
                }
                $phpcsFile->fixer->endChangeset();
            }
        }

        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $stackPtr - 1, null, true);
        if ($tokens[$prev]['line'] < $tokens[$stackPtr]['line']) {
            $isThen = $tokens[$stackPtr]['code'] === T_INLINE_THEN;

            $token = $isThen
                ? $this->findElse($phpcsFile, $stackPtr)
                : $this->findThen($phpcsFile, $stackPtr);

            if ($token === $prev || $token === $next) {
                return;
            }

            $tokenNext = $phpcsFile->findNext(Tokens::$emptyTokens, $token + 1, null, true);
            $tokenPrev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $token - 1, null, true);
            if ($tokens[$tokenNext]['line'] === $tokens[$token]['line']
                && $tokens[$tokenPrev]['line'] === $tokens[$token]['line']
            ) {
                $error = 'Invalid position of ternary operator "%s"';
                $data = [$tokens[$token]['content']];
                $fix = $phpcsFile->addFixableError($error, $token, 'Invalid', $data);

                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();
                    if ($tokens[$token - 1]['code'] === T_WHITESPACE) {
                        $phpcsFile->fixer->replaceToken($token - 1, '');
                    }
                    $phpcsFile->fixer->addNewlineBefore($token);
                    $phpcsFile->fixer->endChangeset();
                }
            }
        }
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return int|null
     */
    protected function findThen(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $count = 0;

        $i = $stackPtr;
        while ($i = $phpcsFile->findPrevious([T_INLINE_ELSE, T_INLINE_THEN], $i - 1)) {
            if ($tokens[$i]['code'] === T_INLINE_ELSE) {
                ++$count;
            } else {
                --$count;

                if ($count < 0) {
                    return $i;
                }
            }
        }

        return null;
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return int|null
     */
    protected function findElse(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $count = 0;

        $i = $stackPtr;
        while ($i = $phpcsFile->findNext([T_INLINE_ELSE, T_INLINE_THEN], $i + 1)) {
            if ($tokens[$i]['code'] === T_INLINE_THEN) {
                ++$count;
            } else {
                --$count;

                if ($count < 0) {
                    return $i;
                }
            }
        }

        return null;
    }
}

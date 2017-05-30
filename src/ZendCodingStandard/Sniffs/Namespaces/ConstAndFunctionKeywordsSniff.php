<?php
namespace ZendCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use ZendCodingStandard\CodingStandard;

class ConstAndFunctionKeywordsSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register()
    {
        return [T_USE];
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if (! CodingStandard::isGlobalUse($phpcsFile, $stackPtr)) {
            return;
        }

        $tokens = $phpcsFile->getTokens();
        $classPtr = $phpcsFile->findNext(
            Tokens::$emptyTokens,
            $stackPtr + 1,
            null,
            true
        );

        $lowerContent = strtolower($tokens[$classPtr]['content']);
        if ($lowerContent === 'function' || $lowerContent === 'const') {
            if ($lowerContent !== $tokens[$classPtr]['content']) {
                $error = 'PHP keywords must be lowercase; expected "%s" but found "%s"';
                $data = [$lowerContent, $tokens[$classPtr]['content']];
                $fix = $phpcsFile->addFixableError($error, $classPtr, 'NotLowerCase', $data);

                if ($fix) {
                    $phpcsFile->fixer->replaceToken($classPtr, $lowerContent);
                }
            }

            if ($tokens[$classPtr + 1]['code'] !== T_WHITESPACE) {
                $error = 'There must be single space after %s keyword';
                $data = [$lowerContent];
                $fix = $phpcsFile->addFixableError($error, $classPtr, 'NoSpace', $data);

                if ($fix) {
                    $phpcsFile->fixer->addContent($classPtr, ' ');
                }
            } elseif ($tokens[$classPtr + 1]['content'] !== ' ') {
                $error = 'There must be single space after %s keyword';
                $data = [$lowerContent];
                $fix = $phpcsFile->addFixableError($error, $classPtr + 1, 'NoSpace', $data);

                if ($fix) {
                    $phpcsFile->fixer->replaceToken($classPtr + 1, ' ');
                }
            }
        }
    }
}

<?php

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use function preg_match;
use function strpos;
use function strtolower;

use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_WHITESPACE;
use const T_FUNCTION;
use const T_STRING;
use const T_WHITESPACE;

class FunctionDataProviderTagSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register()
    {
        return [T_FUNCTION];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $skip = Tokens::$methodPrefixes
            + [T_WHITESPACE => T_WHITESPACE];

        $commentEnd = $phpcsFile->findPrevious($skip, $stackPtr - 1, null, true);
        // There is no doc-comment for the function.
        if ($tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG) {
            return;
        }

        $commentStart = $tokens[$commentEnd]['comment_opener'];
        $tags = $tokens[$commentStart]['comment_tags'];

        // Checks @dataProvider tags
        foreach ($tags as $pos => $tag) {
            if (strtolower($tokens[$tag]['content']) !== '@dataprovider') {
                continue;
            }

            // Check if method name starts from "test".
            $functionPtr = $phpcsFile->findNext(T_FUNCTION, $tag + 1);
            $namePtr = $phpcsFile->findNext(T_STRING, $functionPtr + 1);
            $functionName = $tokens[$namePtr]['content'];

            if (strpos($functionName, 'test') !== 0) {
                $error = 'Tag @dataProvider is allowed only for test* methods.';
                $phpcsFile->addError($error, $tag, 'NoTestMethod');
                return;
            }

            $params = $phpcsFile->getMethodParameters($functionPtr);
            if (! $params) {
                $error = 'Function "%s" does not accept any parameters.';
                $data = [$functionName];
                $phpcsFile->addError($error, $namePtr, 'MissingParameters', $data);
            }

            // Check if data provider name is given and does not have "Provider" suffix.
            if ($tokens[$tag + 1]['code'] !== T_DOC_COMMENT_WHITESPACE
                || $tokens[$tag + 2]['code'] !== T_DOC_COMMENT_STRING
            ) {
                $error = 'Missing data provider name.';
                $phpcsFile->addError($error, $tag, 'MissingName');
            } else {
                $providerName = $tokens[$tag + 2]['content'];

                if (preg_match('/Provider$/', $providerName)) {
                    $error = 'Data provider name should have "Provider" suffix.';
                    $phpcsFile->addError($error, $tag, 'DataProviderInvalidName');
                }
            }
        }
    }
}

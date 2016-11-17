<?php
/**
 * Below class is mixture of:
 * @see http://jdon.at/1h0wb
 * @see https://github.com/squizlabs/PHP_CodeSniffer/pull/1106
 *
 * @todo remove once merged to squizlabs/PHP_CodeSniffer (?)
 */
namespace ZendCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;
use PHP_CodeSniffer_Tokens;

/**
 * Checks for "use" statements that are not needed in a file.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author    Jesse Donat <donatj@gmail.com>
 * @copyright 2016 Capstone Digital
 */
class UnusedUseStatementSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_USE];
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the current token in
     *                      the stack passed in $tokens.
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Only check use statements in the global scope.
        if (! empty($tokens[$stackPtr]['conditions'])) {
            return;
        }

        // Seek to the end of the statement and get the string before the semi colon.
        $semiColon = $phpcsFile->findEndOfStatement($stackPtr);
        if ($tokens[$semiColon]['code'] !== T_SEMICOLON) {
            return;
        }

        $classPtr = $phpcsFile->findPrevious(
            PHP_CodeSniffer_Tokens::$emptyTokens,
            $semiColon - 1,
            null,
            true
        );

        if ($tokens[$classPtr]['code'] !== T_STRING) {
            return;
        }

        // Search where the class name is used. PHP treats class names case
        // insensitive, that's why we cannot search for the exact class name string
        // and need to iterate over all T_STRING tokens in the file.
        $classUsed = $phpcsFile->findNext([T_STRING, T_DOC_COMMENT_STRING], $classPtr + 1);
        $className = $tokens[$classPtr]['content'];
        $lowerClassName = strtolower($className);

        // Check if the referenced class is in the same namespace as the current
        // file. If it is then the use statement is not necessary.
        $namespacePtr = $phpcsFile->findPrevious([T_NAMESPACE], $stackPtr);

        // Check if the use statement does aliasing with the "as" keyword. Aliasing
        // is allowed even in the same namespace.
        $aliasUsed = $phpcsFile->findPrevious(T_AS, $classPtr - 1, $stackPtr);

        if ($namespacePtr !== false && $aliasUsed === false) {
            $nsEnd = $phpcsFile->findNext(
                [
                    T_NS_SEPARATOR,
                    T_STRING,
                    T_DOC_COMMENT_STRING,
                    T_WHITESPACE,
                ],
                $namespacePtr + 1,
                null,
                true
            );
            $namespace = trim($phpcsFile->getTokensAsString($namespacePtr + 1, $nsEnd - $namespacePtr - 1));

            $useNamespacePtr = $phpcsFile->findNext([T_STRING], $stackPtr + 1);
            $useNamespaceEnd = $phpcsFile->findNext(
                [
                    T_NS_SEPARATOR,
                    T_STRING,
                ],
                $useNamespacePtr + 1,
                null,
                true
            );
            $useNamespace = rtrim(
                $phpcsFile->getTokensAsString(
                    $useNamespacePtr,
                    $useNamespaceEnd - $useNamespacePtr - 1
                ),
                '\\'
            );

            if (strcasecmp($namespace, $useNamespace) === 0) {
                $classUsed = false;
            }
        }

        $emptyTokens = PHP_CodeSniffer_Tokens::$emptyTokens;
        unset($emptyTokens[T_DOC_COMMENT_TAG]);


        while ($classUsed !== false) {
            if (($tokens[$classUsed]['code'] == T_STRING
                    && strtolower($tokens[$classUsed]['content']) === $lowerClassName)
                || ($tokens[$classUsed]['code'] == T_DOC_COMMENT_STRING
                    && preg_match(
                        '/(\s|\||^)' . preg_quote($lowerClassName) . '(\s|\||$|\[\])/i',
                        $tokens[$classUsed]['content']
                    ))
            ) {
                $beforeUsage = $phpcsFile->findPrevious(
                    $emptyTokens,
                    $classUsed - 1,
                    null,
                    true
                );

                if ($tokens[$classUsed]['code'] == T_STRING) {
                    // If a backslash is used before the class name then this is some other
                    // use statement.
                    if ($tokens[$beforeUsage]['code'] !== T_USE
                        && $tokens[$beforeUsage]['code'] !== T_NS_SEPARATOR
                    ) {
                        return;
                    }

                    // Trait use statement within a class.
                    if ($tokens[$beforeUsage]['code'] === T_USE
                        && ! empty($tokens[$beforeUsage]['conditions'])
                    ) {
                        return;
                    }
                } else {
                    if ($tokens[$beforeUsage]['code'] === T_DOC_COMMENT_TAG &&
                        in_array(
                            $tokens[$beforeUsage]['content'],
                            ['@var', '@param', '@return', '@throws', '@method']
                        )
                    ) {
                        return;
                    }
                }
            }

            $classUsed = $phpcsFile->findNext([T_STRING, T_DOC_COMMENT_STRING], $classUsed + 1);
        }

        $warning = 'Unused use statement: ' . $className;
        $fix = $phpcsFile->addFixableWarning($warning, $stackPtr, 'UnusedUse');

        if ($fix) {
            // Remove the whole use statement line.
            $phpcsFile->fixer->beginChangeset();
            for ($i = $stackPtr; $i <= $semiColon; $i++) {
                $phpcsFile->fixer->replaceToken($i, '');
            }

            // Also remove whitespace after the semicolon (new lines).
            while (isset($tokens[$i]) && $tokens[$i]['code'] === T_WHITESPACE) {
                $phpcsFile->fixer->replaceToken($i, '');
                if (strpos($tokens[$i]['content'], $phpcsFile->eolChar) !== false) {
                    break;
                }

                ++$i;
            }

            $phpcsFile->fixer->endChangeset();
        }
    }
}

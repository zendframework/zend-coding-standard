<?php

declare(strict_types=1);

namespace ZendCodingStandard\Helper;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use ZendCodingStandard\CodingStandard;

use function ltrim;
use function strrchr;
use function strtolower;
use function strtoupper;
use function substr;
use function trim;

use const T_AS;
use const T_NAMESPACE;
use const T_NS_SEPARATOR;
use const T_SEMICOLON;
use const T_STRING;
use const T_USE;
use const T_WHITESPACE;

/**
 * @internal
 */
trait Namespaces
{
    private function getNamespace(File $phpcsFile, int $stackPtr) : string
    {
        if ($nsStart = $phpcsFile->findPrevious(T_NAMESPACE, $stackPtr - 1)) {
            $nsEnd = $phpcsFile->findNext([T_NS_SEPARATOR, T_STRING, T_WHITESPACE], $nsStart + 1, null, true);
            return trim($phpcsFile->getTokensAsString($nsStart + 1, $nsEnd - $nsStart - 1));
        }

        return '';
    }

    /**
     * Returns array of imported classes. Key is lowercase name, and value is FQCN.
     *
     * @return string[][]
     */
    private function getGlobalUses(File $phpcsFile, int $stackPtr = 0) : array
    {
        $first = 0;
        $last = $phpcsFile->numTokens;

        $tokens = $phpcsFile->getTokens();

        $nsStart = $phpcsFile->findPrevious(T_NAMESPACE, $stackPtr);
        if ($nsStart && isset($tokens[$nsStart]['scope_opener'])) {
            $first = $tokens[$nsStart]['scope_opener'];
            $last = $tokens[$nsStart]['scope_closer'];
        }

        $imports = [];

        $use = $first;
        while ($use = $phpcsFile->findNext(T_USE, $use + 1, $last)) {
            if (! empty($tokens[$use]['conditions'])) {
                continue;
            }

            $nextToken = $phpcsFile->findNext(T_WHITESPACE, $use + 1, null, true);

            $end = $phpcsFile->findNext(
                [T_NS_SEPARATOR, T_STRING],
                $nextToken + 1,
                null,
                true
            );

            $class = trim($phpcsFile->getTokensAsString($nextToken, $end - $nextToken));

            $endOfStatement = $phpcsFile->findEndOfStatement($use);
            if ($aliasStart = $phpcsFile->findNext([T_WHITESPACE, T_AS], $end + 1, $endOfStatement, true)) {
                $alias = trim($phpcsFile->getTokensAsString($aliasStart, $endOfStatement - $aliasStart));
            } else {
                if (strrchr($class, '\\') !== false) {
                    $alias = substr(strrchr($class, '\\'), 1);
                } else {
                    $alias = $class;
                }
            }

            $imports[strtolower($alias)] = ['alias' => $alias, 'class' => $class];
        }

        return $imports;
    }

    /**
     * @return string[][]
     */
    private function getImportedConstants(File $phpcsFile, int $stackPtr, ?int &$lastUse) : array
    {
        $first = 0;
        $last = $phpcsFile->numTokens;

        $tokens = $phpcsFile->getTokens();

        $nsStart = $phpcsFile->findPrevious(T_NAMESPACE, $stackPtr);
        if ($nsStart && isset($tokens[$nsStart]['scope_opener'])) {
            $first = $tokens[$nsStart]['scope_opener'];
            $last = $tokens[$nsStart]['scope_closer'];
        }

        $lastUse = null;
        $constants = [];

        $use = $first;
        while ($use = $phpcsFile->findNext(T_USE, $use + 1, $last)) {
            if (CodingStandard::isGlobalUse($phpcsFile, $use)) {
                $next = $phpcsFile->findNext(Tokens::$emptyTokens, $use + 1, null, true);
                if ($tokens[$next]['code'] === T_STRING
                    && strtolower($tokens[$next]['content']) === 'const'
                ) {
                    $start = $phpcsFile->findNext([T_STRING, T_NS_SEPARATOR], $next + 1);
                    $end = $phpcsFile->findPrevious(
                        T_STRING,
                        $phpcsFile->findNext([T_AS, T_SEMICOLON], $start + 1) - 1
                    );
                    $endOfStatement = $phpcsFile->findEndOfStatement($next);
                    $name = $phpcsFile->findPrevious(T_STRING, $endOfStatement - 1);
                    $fullName = $phpcsFile->getTokensAsString($start, $end - $start + 1);

                    $constants[strtoupper($tokens[$name]['content'])] = [
                        'name' => $tokens[$name]['content'],
                        'fqn'  => ltrim($fullName, '\\'),
                    ];

                    $lastUse = $use;
                }
            }

            if (! $lastUse) {
                $lastUse = $use;
            }
        }

        return $constants;
    }

    /**
     * @return string[][]
     */
    private function getImportedFunctions(File $phpcsFile, int $stackPtr, ?int &$lastUse) : array
    {
        $first = 0;
        $last = $phpcsFile->numTokens;

        $tokens = $phpcsFile->getTokens();

        $nsStart = $phpcsFile->findPrevious(T_NAMESPACE, $stackPtr);
        if ($nsStart && isset($tokens[$nsStart]['scope_opener'])) {
            $first = $tokens[$nsStart]['scope_opener'];
            $last = $tokens[$nsStart]['scope_closer'];
        }

        $lastUse = null;
        $functions = [];

        $use = $first;
        while ($use = $phpcsFile->findNext(T_USE, $use + 1, $last)) {
            if (CodingStandard::isGlobalUse($phpcsFile, $use)) {
                $next = $phpcsFile->findNext(Tokens::$emptyTokens, $use + 1, null, true);
                if ($tokens[$next]['code'] === T_STRING
                    && strtolower($tokens[$next]['content']) === 'function'
                ) {
                    $start = $phpcsFile->findNext([T_STRING, T_NS_SEPARATOR], $next + 1);
                    $end = $phpcsFile->findPrevious(
                        T_STRING,
                        $phpcsFile->findNext([T_AS, T_SEMICOLON], $start + 1) - 1
                    );
                    $endOfStatement = $phpcsFile->findEndOfStatement($next);
                    $name = $phpcsFile->findPrevious(T_STRING, $endOfStatement - 1);
                    $fullName = $phpcsFile->getTokensAsString($start, $end - $start + 1);

                    $functions[strtolower($tokens[$name]['content'])] = [
                        'name' => $tokens[$name]['content'],
                        'fqn'  => ltrim($fullName, '\\'),
                    ];

                    $lastUse = $use;
                }
            }

            if (! $lastUse) {
                $lastUse = $use;
            }
        }

        return $functions;
    }
}

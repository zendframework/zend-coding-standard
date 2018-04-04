<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYRIGHT.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use ZendCodingStandard\CodingStandard;

use function array_map;
use function end;
use function implode;
use function reset;
use function str_replace;
use function strcasecmp;
use function trim;
use function uasort;

use const T_ANON_CLASS;
use const T_CLASS;
use const T_CLOSE_CURLY_BRACKET;
use const T_COMMA;
use const T_OPEN_CURLY_BRACKET;
use const T_SEMICOLON;
use const T_TRAIT;
use const T_USE;

class AlphabeticallySortedTraitsSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_CLASS, T_TRAIT, T_ANON_CLASS];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $uses = $this->getTraits($phpcsFile, $stackPtr);

        $lastUse = null;
        foreach ($uses as $use) {
            if (! $lastUse) {
                $lastUse = $use;
                continue;
            }

            $order = $this->compareUseStatements($use, $lastUse);

            if ($order < 0) {
                $error = 'Traits are incorrectly ordered. The first wrong one is %s';
                $data = [$use['name']];
                $fix = $phpcsFile->addFixableError($error, $use['ptrUse'], 'IncorrectOrder', $data);

                if ($fix) {
                    $this->fixAlphabeticalOrder($phpcsFile, $uses);
                }

                return;
            }

            $lastUse = $use;
        }
    }

    /**
     * @return string[][]
     */
    private function getTraits(File $phpcsFile, int $scopePtr) : array
    {
        $tokens = $phpcsFile->getTokens();

        $uses = [];

        $start = $tokens[$scopePtr]['scope_opener'];
        $end = $tokens[$scopePtr]['scope_closer'];
        while ($use = $phpcsFile->findNext(T_USE, $start + 1, $end)) {
            if (! CodingStandard::isTraitUse($phpcsFile, $use)
                || ! isset($tokens[$use]['conditions'][$scopePtr])
                || $tokens[$use]['level'] !== $tokens[$scopePtr]['level'] + 1
            ) {
                $start = $use;
                continue;
            }

            // Find comma, semicolon or opening curly bracket, whatever is first.
            $endOfName = $phpcsFile->findNext(
                [T_COMMA, T_SEMICOLON, T_OPEN_CURLY_BRACKET],
                $use + 1
            );

            // Find end of scope - could be semicolon or closing curly bracket.
            $endOfScope = $this->getEndOfTraitScope($phpcsFile, $endOfName);

            $uses[] = [
                'ptrUse' => $use,
                'name' => trim($phpcsFile->getTokensAsString($use + 1, $endOfName - $use - 1)),
                'ptrEnd' => $endOfScope,
                'string' => trim($phpcsFile->getTokensAsString($use, $endOfScope - $use + 1)),
            ];

            $start = $endOfName;
        }

        return $uses;
    }

    private function getEndOfTraitScope(File $phpcsFile, int $stackPtr) : int
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_COMMA) {
            $stackPtr = $phpcsFile->findNext([T_SEMICOLON, T_OPEN_CURLY_BRACKET], $stackPtr + 1);
        }

        if ($tokens[$stackPtr]['code'] === T_SEMICOLON) {
            return $stackPtr;
        }

        return $phpcsFile->findNext(T_CLOSE_CURLY_BRACKET, $stackPtr + 1);
    }

    /**
     * @param string[] $a
     * @param string[] $b
     */
    private function compareUseStatements(array $a, array $b) : int
    {
        return strcasecmp(
            $this->clearName($a['name']),
            $this->clearName($b['name'])
        );
    }

    private function clearName(string $name) : string
    {
        return str_replace('\\', ':', $name);
    }

    /**
     * @param string[][] $uses
     */
    private function fixAlphabeticalOrder(File $phpcsFile, array $uses) : void
    {
        $first = reset($uses);
        $last = end($uses);
        $lastScopeCloser = $last['ptrEnd'];

        $phpcsFile->fixer->beginChangeset();
        for ($i = $first['ptrUse']; $i <= $lastScopeCloser; ++$i) {
            $phpcsFile->fixer->replaceToken($i, '');
        }

        uasort($uses, function (array $a, array $b) {
            return $this->compareUseStatements($a, $b);
        });

        $phpcsFile->fixer->addContent($first['ptrUse'], implode($phpcsFile->eolChar, array_map(function ($use) {
            return $use['string'];
        }, $uses)));

        $phpcsFile->fixer->endChangeset();
    }
}

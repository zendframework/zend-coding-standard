<?php
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
     * @inheritDoc
     */
    public function register()
    {
        return [T_CLASS, T_TRAIT, T_ANON_CLASS];
    }

    /**
     * @inheritDoc
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
     * @param File $phpcsFile
     * @param int $scopePtr
     * @return array[]
     */
    private function getTraits(File $phpcsFile, $scopePtr)
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
                'name'   => trim($phpcsFile->getTokensAsString($use + 1, $endOfName - $use - 1)),
                'ptrEnd' => $endOfScope,
                'string' => trim($phpcsFile->getTokensAsString($use, $endOfScope - $use + 1)),
            ];

            $start = $endOfName;
        }

        return $uses;
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return int
     */
    private function getEndOfTraitScope(File $phpcsFile, $stackPtr)
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
     * @param array $a
     * @param array $b
     * @return int
     */
    private function compareUseStatements(array $a, array $b)
    {
        return strcasecmp(
            $this->clearName($a['name']),
            $this->clearName($b['name'])
        );
    }

    /**
     * @param string $name
     * @return string
     */
    private function clearName($name)
    {
        return str_replace('\\', ':', $name);
    }

    /**
     * @param File $phpcsFile
     * @param array[] $uses
     * @return void
     */
    private function fixAlphabeticalOrder(File $phpcsFile, array $uses)
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

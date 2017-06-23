<?php
namespace ZendCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use ZendCodingStandard\CodingStandard;

class AlphabeticallySortedUsesSniff implements Sniff
{
    /**
     * @inheritDoc
     */
    public function register()
    {
        return [T_NAMESPACE];
    }

    /**
     * @inheritDoc
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $uses = $this->getUseStatements($phpcsFile, $stackPtr);

        $lastUse = null;
        foreach ($uses as $use) {
            if (! $lastUse) {
                $lastUse = $use;
                continue;
            }

            $order = $this->compareUseStatements($use, $lastUse);

            if ($order < 0) {
                $error = 'Use statements are incorrectly ordered. The first wrong one is %s';
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
    private function getUseStatements(File $phpcsFile, $scopePtr)
    {
        $tokens = $phpcsFile->getTokens();

        $uses = [];

        if (isset($tokens[$scopePtr]['scope_opener'])) {
            $start = $tokens[$scopePtr]['scope_opener'];
            $end = $tokens[$scopePtr]['scope_closer'];
        } else {
            $start = $scopePtr;
            $end = null;
        }
        while ($use = $phpcsFile->findNext(T_USE, $start + 1, $end)) {
            if (! CodingStandard::isGlobalUse($phpcsFile, $use)
                || ($end !== null
                    && (! isset($tokens[$use]['conditions'][$scopePtr])
                        || $tokens[$use]['level'] !== $tokens[$scopePtr]['level'] + 1))
            ) {
                $start = $use;
                continue;
            }

            // find semicolon as the end of the global use scope
            $endOfScope = $phpcsFile->findNext([T_SEMICOLON], $use + 1);

            $startOfName = $phpcsFile->findNext([T_STRING, T_NS_SEPARATOR], $use + 1, $endOfScope);

            $type = 'class';
            if ($tokens[$startOfName]['code'] === T_STRING) {
                $lowerContent = strtolower($tokens[$startOfName]['content']);
                if ($lowerContent === 'function'
                    || $lowerContent === 'const'
                ) {
                    $type = $lowerContent;

                    $startOfName = $phpcsFile->findNext([T_STRING, T_NS_SEPARATOR], $startOfName + 1, $endOfScope);
                }
            }

            $uses[] = [
                'ptrUse' => $use,
                'name'   => trim($phpcsFile->getTokensAsString($startOfName, $endOfScope - $startOfName)),
                'ptrEnd' => $endOfScope,
                'string' => trim($phpcsFile->getTokensAsString($use, $endOfScope - $use + 1)),
                'type'   => $type,
            ];

            $start = $endOfScope;
        }

        return $uses;
    }

    /**
     * @param array $a
     * @param array $b
     * @return int
     */
    private function compareUseStatements(array $a, array $b)
    {
        if ($a['type'] === $b['type']) {
            return strcasecmp(
                $this->clearName($a['name']),
                $this->clearName($b['name'])
            );
        }

        if ($a['type'] === 'class'
            || ($a['type'] === 'function' && $b['type'] === 'const')
        ) {
            return -1;
        }

        return 1;
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

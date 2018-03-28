<?php

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use ZendCodingStandard\CodingStandard;

use function end;
use function implode;
use function reset;
use function str_replace;
use function strcasecmp;
use function strpos;
use function strtolower;
use function trim;
use function uasort;

use const T_NAMESPACE;
use const T_NS_SEPARATOR;
use const T_SEMICOLON;
use const T_STRING;
use const T_USE;

class AlphabeticallySortedUsesSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_NAMESPACE];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $uses = $this->getUseStatements($phpcsFile, $stackPtr);
        $tokens = $phpcsFile->getTokens();

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

            // Check empty lines between use statements.
            // There must be exactly one empty line between use statements of different type
            // and no empty lines between use statements of the same type.
            $lineDiff = $tokens[$use['ptrUse']]['line'] - $tokens[$lastUse['ptrUse']]['line'];
            if ($lastUse['type'] === $use['type']) {
                if ($lineDiff > 1) {
                    $error = 'There must not be any empty line between use statement of the same type';
                    $fix = $phpcsFile->addFixableError($error, $use['ptrUse'], 'EmptyLine');

                    if ($fix) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = $lastUse['ptrEnd'] + 1; $i < $use['ptrUse']; ++$i) {
                            if (strpos($tokens[$i]['content'], $phpcsFile->eolChar) !== false) {
                                $phpcsFile->fixer->replaceToken($i, '');
                                --$lineDiff;

                                if ($lineDiff === 1) {
                                    break;
                                }
                            }
                        }
                        $phpcsFile->fixer->endChangeset();
                    }
                } elseif ($lineDiff === 0) {
                    $error = 'Each use statement must be in new line';
                    $fix = $phpcsFile->addFixableError($error, $use['ptrUse'], 'TheSameLine');

                    if ($fix) {
                        $phpcsFile->fixer->addNewline($lastUse['ptrEnd']);
                    }
                }
            } else {
                if ($lineDiff > 2) {
                    $error = 'There must be exactly one empty line between use statements of different type';
                    $fix = $phpcsFile->addFixableError($error, $use['ptrUse'], 'TooManyEmptyLines');

                    if ($fix) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = $lastUse['ptrEnd'] + 1; $i < $use['ptrUse']; ++$i) {
                            if (strpos($tokens[$i]['content'], $phpcsFile->eolChar) !== false) {
                                $phpcsFile->fixer->replaceToken($i, '');
                                --$lineDiff;

                                if ($lineDiff === 2) {
                                    break;
                                }
                            }
                        }
                        $phpcsFile->fixer->endChangeset();
                    }
                } elseif ($lineDiff <= 1) {
                    $error = 'There must be exactly one empty line between use statements of different type';
                    $fix = $phpcsFile->addFixableError($error, $use['ptrUse'], 'MissingEmptyLine');

                    if ($fix) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = $lineDiff; $i < 2; ++$i) {
                            $phpcsFile->fixer->addNewline($lastUse['ptrEnd']);
                        }
                        $phpcsFile->fixer->endChangeset();
                    }
                }
            }

            $lastUse = $use;
        }
    }

    /**
     * @return string[][]
     */
    private function getUseStatements(File $phpcsFile, int $scopePtr) : array
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
                'name' => trim($phpcsFile->getTokensAsString($startOfName, $endOfScope - $startOfName)),
                'ptrEnd' => $endOfScope,
                'string' => trim($phpcsFile->getTokensAsString($use, $endOfScope - $use + 1)),
                'type' => $type,
            ];

            $start = $endOfScope;
        }

        return $uses;
    }

    /**
     * @param string[] $a
     * @param string[] $b
     */
    private function compareUseStatements(array $a, array $b) : int
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

        $lastType = reset($uses)['type'];
        $content = [];
        foreach ($uses as $use) {
            if ($lastType !== $use['type']) {
                $content[] = $phpcsFile->eolChar . $use['string'];
                $lastType = $use['type'];
            } else {
                $content[] = $use['string'];
            }
        }

        $phpcsFile->fixer->addContent($first['ptrUse'], implode($phpcsFile->eolChar, $content));
        $phpcsFile->fixer->endChangeset();
    }
}

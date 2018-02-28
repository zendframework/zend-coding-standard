<?php

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use function array_filter;
use function array_search;
use function current;
use function in_array;
use function key;
use function next;
use function strpos;
use function strtolower;
use function substr;
use function uasort;

use const T_COMMENT;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_STAR;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_WHITESPACE;
use const T_FUNCTION;
use const T_WHITESPACE;

class FunctionCommentSniff implements Sniff
{
    /**
     * @var string[]
     */
    public $tagOrder = [
        '@dataProvider',
        '@param',
        '@return',
        '@throws',
    ];

    /**
     * @var string[]
     */
    public $blankLineBefore = [
        '@dataProvider',
        '@param',
    ];

    /**
     * @var string[]
     */
    public $nestedTags = [
        '@var',
    ];

    /**
     * @return int[]
     */
    public function register() : array
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
        if ($tokens[$commentEnd]['code'] === T_COMMENT) {
            $error = 'You must use "/**" style comments for a function comment';
            $phpcsFile->addError($error, $stackPtr, 'WrongStyle');
            return;
        }

        $commentStart = null;
        if ($tokens[$commentEnd]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
            if ($tokens[$commentEnd]['line'] !== $tokens[$stackPtr]['line'] - 1) {
                $error = 'There must be no blank lines after the function comment';
                $phpcsFile->addError($error, $commentEnd, 'SpacingAfter');
            }

            $commentStart = $tokens[$commentEnd]['comment_opener'];

            $this->processTagOrder($phpcsFile, $commentStart);

            if ($tokens[$commentStart]['line'] === $tokens[$commentEnd]['line']) {
                $error = 'Function comment must be multiline comment';
                $fix = $phpcsFile->addFixableError($error, $commentStart, 'SingleLine');

                if ($fix) {
                    $phpcsFile->fixer->addContent($commentStart, $phpcsFile->eolChar . ' *');
                }
            }
        }
    }

    private function processTagOrder(File $phpcsFile, int $commentStart) : void
    {
        $tokens = $phpcsFile->getTokens();

        $tags = $tokens[$commentStart]['comment_tags'];

        $nestedTags = [];
        $data = [];
        while ($tag = current($tags)) {
            $key = key($tags);
            if (isset($tags[$key + 1])) {
                $lastFrom = $tags[$key + 1];
            } else {
                $lastFrom = $tokens[$commentStart]['comment_closer'];
            }

            $last = $phpcsFile->findPrevious(
                [T_DOC_COMMENT_STAR, T_DOC_COMMENT_WHITESPACE],
                $lastFrom - 1,
                null,
                true
            );

            // if the last character of the description is {
            // we need to find closing curly bracket and treat the whole block
            // as one, skip tags inside if there any
            if (substr($tokens[$last]['content'], -1) === '{') {
                $dep = 1;
                $i = $last;
                $max = $tokens[$commentStart]['comment_closer'];
                while ($dep > 0 && $i < $max) {
                    $i = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $i + 1, $max);

                    if (! $i) {
                        break;
                    }

                    if ($tokens[$i]['content'][0] === '}') {
                        --$dep;
                    }

                    if (substr($tokens[$i]['content'], -1) === '{') {
                        ++$dep;
                    }
                }

                if ($dep > 0) {
                    $error = 'Tag contains nested description, but cannot find the closing bracket';
                    $phpcsFile->addError($error, $last, 'NotClosed');
                    return;
                }

                $last = $i;
                while (isset($tags[$key + 1]) && $tags[$key + 1] < $i) {
                    $tagName = strtolower($tokens[$tags[$key + 1]]['content']);
                    if (! array_filter($this->nestedTags, function ($v) use ($tagName) {
                        return strtolower($v) === $tagName;
                    })) {
                        $error = 'Tag %s cannot be nested.';
                        $data = [
                            $tokens[$tags[$key + 1]]['content'],
                        ];
                        $phpcsFile->addError($error, $tags[$key + 1], 'NestedTag', $data);
                        return;
                    }

                    $nestedTags[] = $tags[$key + 1];

                    next($tags);
                    ++$key;
                }
            }

            while ($tokens[$last + 1]['line'] === $tokens[$last]['line']) {
                ++$last;
            }

            $data[] = [
                'tag'   => strtolower($tokens[$tag]['content']),
                'token' => $tag,
                'first' => $phpcsFile->findFirstOnLine([], $tag, true),
                'last'  => $last,
            ];

            next($tags);
        }

        $firstTag = current($data);

        // Sorts values only and keep unchanged keys.
        uasort($data, function (array $a, array $b) use ($data) {
            $ai = array_search($a['tag'], $this->tagOrder, true);
            $bi = array_search($b['tag'], $this->tagOrder, true);

            if ($ai !== false && $bi !== false && $ai !== $bi) {
                return $ai > $bi ? 1 : -1;
            }

            if ($ai !== false && $bi === false) {
                return 1;
            }

            if ($ai === false && $bi !== false) {
                return -1;
            }

            return array_search($a, $data, true) > array_search($b, $data, true) ? 1 : -1;
        });

        $last = key($data);
        foreach ($data as $key => $val) {
            if ($last <= $key) {
                $last = $key;
                continue;
            }

            $error = 'Tags are ordered incorrectly. Here is the first in wrong order';
            $fix = $phpcsFile->addFixableError($error, $val['token'], 'InvalidOrder');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $toAdd = [];
                foreach ($data as $k => $v) {
                    $toAdd[] = $phpcsFile->getTokensAsString($v['first'], $v['last'] - $v['first'] + 1);
                    for ($i = $v['first']; $i <= $v['last']; ++$i) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }
                }
                foreach ($toAdd as $content) {
                    $phpcsFile->fixer->addContent($firstTag['first'], $content);
                }
                $phpcsFile->fixer->endChangeset();
            }
            return;
        }

        // If order is correct check empty lines between tags.
        $skip = [T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STAR];
        foreach ($tags as $key => $tag) {
            // Skip the first tag. Empty line after description is added in other sniff.
            if ($key === 0) {
                continue;
            }

            $i = $key;
            do {
                $prevTag = $tags[--$i];
            } while (in_array($prevTag, $nestedTags, true));
            if (in_array($tokens[$tag]['content'], $this->blankLineBefore, true)
                && $tokens[$prevTag]['content'] !== $tokens[$tag]['content']
            ) {
                $expected = 1;
            } else {
                $expected = 0;
            }

            $prev = $phpcsFile->findPrevious($skip, $tag - 1, null, true);
            $found = $tokens[$tag]['line'] - $tokens[$prev]['line'] - 1;
            if ($found !== $expected) {
                $error = 'Invalid number of empty lines between tags; expected %d, but found %d';
                $data = [
                    $expected,
                    $found,
                ];
                $fix = $phpcsFile->addFixableError($error, $prev + 2, 'BlankLine', $data);

                if ($fix) {
                    if ($found > $expected) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = $prev + 1; $i < $tag; ++$i) {
                            if ($tokens[$i]['code'] === T_DOC_COMMENT_WHITESPACE
                                && strpos($tokens[$i]['content'], $phpcsFile->eolChar) !== false
                            ) {
                                if ($found === $expected) {
                                    break;
                                }

                                --$found;
                            }

                            $phpcsFile->fixer->replaceToken($i, '');
                        }
                        $phpcsFile->fixer->endChangeset();
                    } else {
                        $phpcsFile->fixer->addContent($prev, $phpcsFile->eolChar . '*');
                    }
                }
            }
        }
    }
}

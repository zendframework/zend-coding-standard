<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYRIGHT.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\Files;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use function array_map;
use function array_search;
use function stripos;
use function strtolower;

use const T_DECLARE;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_OPEN_TAG;
use const T_STRING;
use const T_WHITESPACE;

class DeclareStrictTypesSniff implements Sniff
{
    /**
     * @var string[]
     */
    public $containsTags = [
        '@copyright',
        '@license',
        '@see',
    ];

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_DECLARE];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $string = $phpcsFile->findNext(
            T_STRING,
            $tokens[$stackPtr]['parenthesis_opener'] + 1,
            $tokens[$stackPtr]['parenthesis_closer']
        );

        // It is no strict type declaration.
        if ($string === false
            || stripos($tokens[$string]['content'], 'strict_types') === false
        ) {
            return;
        }

        $prev = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);

        if ($tokens[$prev]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
            if ($this->checkTags($phpcsFile, $tokens[$tokens[$prev]['comment_opener']])) {
                return;
            }
        }

        $eos = $phpcsFile->findEndOfStatement($stackPtr);

        if ($tokens[$prev]['code'] !== T_OPEN_TAG) {
            $error = 'Wrong place of strict type declaration statement; must be above comment';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'BelowComment');

            if ($fix) {
                $prev = $phpcsFile->findPrevious([T_OPEN_TAG, T_DOC_COMMENT_CLOSE_TAG], $prev - 1);
                $this->fix($phpcsFile, $stackPtr, $eos, $prev);
            }

            return;
        }

        $next = $phpcsFile->findNext(T_WHITESPACE, $eos + 1, null, true);
        if ($tokens[$next]['code'] === T_DOC_COMMENT_OPEN_TAG
            && $this->checkTags($phpcsFile, $tokens[$next])
        ) {
            $error = 'Wrong place of strict type declaration statement; must be below comment';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'AboveComment');

            if ($fix) {
                $this->fix($phpcsFile, $stackPtr, $eos, $tokens[$next]['comment_closer']);
            }
        }
    }

    private function fix(File $phpcsFile, int $start, int $eos, int $after) : void
    {
        $declaration = $phpcsFile->getTokensAsString($start, $eos - $start + 1);

        $phpcsFile->fixer->beginChangeset();
        $phpcsFile->fixer->addContent($after, $phpcsFile->eolChar . $declaration . $phpcsFile->eolChar);
        for ($i = $start; $i <= $eos; ++$i) {
            $phpcsFile->fixer->replaceToken($i, '');
        }
        $phpcsFile->fixer->endChangeset();
    }

    private function checkTags(File $phpcsFile, array $tag) : bool
    {
        $tokens = $phpcsFile->getTokens();

        $tags = array_map(function ($value) {
            return strtolower($value);
        }, $this->containsTags);

        foreach ($tag['comment_tags'] ?? [] as $token) {
            if (false !== ($i = array_search(strtolower($tokens[$token]['content']), $tags, true))) {
                unset($tags[$i]);
            }
        }

        return ! $tags;
    }
}

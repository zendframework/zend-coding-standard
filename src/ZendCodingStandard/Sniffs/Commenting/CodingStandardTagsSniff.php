<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYRIGHT.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use function array_filter;
use function key;
use function ltrim;
use function preg_quote;
use function preg_replace;
use function stripos;
use function strtolower;

use const ARRAY_FILTER_USE_KEY;
use const T_COMMENT;
use const T_DOC_COMMENT_TAG;
use const T_OPEN_TAG;

/**
 * Token works on @codingStandards* tags which normally are ignored.
 * Since PHP_CodeSniffer these are deprecated and replaced with phpcs:
 * tags. Sniff temporarily replaces token content to allow this sniff
 * to process.
 *
 * It doesn't work with whole ignored files, as these
 * are not processed at all.
 */
class CodingStandardTagsSniff implements Sniff
{
    /**
     * @var string[]
     */
    private $replacements = [
        '@codingStandardsIgnoreFile' => '@phpcs:ignoreFile',
        '@codingStandardsIgnoreStart' => '@phpcs:disable',
        '@codingStandardsIgnoreEnd' => '@phpcs:enable',
        '@codingStandardsIgnoreLine' => '@phpcs:ignore',
        '@codingStandardsChangeSetting' => '@phpcs:set',
    ];

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_OPEN_TAG];
    }

    /**
     * @param int $stackPtr
     * @return int
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $ignoredLines = $phpcsFile->tokenizer->ignoredLines;
        $phpcsFile->tokenizer->ignoredLines = [];

        $next = $stackPtr;
        while ($next = $phpcsFile->findNext([T_COMMENT, T_DOC_COMMENT_TAG], $next + 1)) {
            if ($tokens[$next]['code'] === T_DOC_COMMENT_TAG) {
                $lower = strtolower($tokens[$next]['content']);
                if ($tag = key(array_filter($this->replacements, function ($key) use ($lower) {
                    return strtolower($key) === $lower;
                }, ARRAY_FILTER_USE_KEY))) {
                    $this->overrideToken($phpcsFile, $next);

                    $error = 'PHP_CodeSniffer tag %s in line %d is deprecated; use %s instead';
                    $data = [
                        $tag,
                        $tokens[$next]['line'],
                        $this->replacements[$tag],
                    ];
                    $fix = $phpcsFile->addFixableError($error, $next, 'DeprecatedTag', $data);

                    if ($fix) {
                        $phpcsFile->fixer->replaceToken($next, $this->replacements[$tag]);
                    }
                }

                continue;
            }

            $content = ltrim($tokens[$next]['content'], ' /*');
            foreach ($this->replacements as $old => $new) {
                if (stripos($content, $old) !== false) {
                    $this->overrideToken($phpcsFile, $next);

                    $error = 'PHP_CodeSniffer tag %s in line is deprecated; use %s instead';
                    $data = [
                        $old,
                        $tokens[$next]['line'],
                        $new,
                    ];
                    $fix = $phpcsFile->addFixableError($error, $next, 'DeprecatedTag', $data);

                    if ($fix) {
                        $content = preg_replace(
                            '/' . preg_quote($old, '/') . '/i',
                            $new,
                            $tokens[$next]['content']
                        );
                        $phpcsFile->fixer->replaceToken($next, $content);
                    }
                    break;
                }
            }
        }

        $phpcsFile->tokenizer->ignoredLines = $ignoredLines;
        return $phpcsFile->numTokens + 1;
    }

    private function overrideToken(File $phpcsFile, int $stackPtr)
    {
        $clear = function () use ($stackPtr) {
            $this->tokens[$stackPtr]['content'] = 'ZF-CS';
        };

        $clear->bindTo($phpcsFile, File::class)();
    }
}

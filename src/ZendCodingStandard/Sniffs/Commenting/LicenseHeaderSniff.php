<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYRIGHT.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use function array_merge;
use function stripos;
use function strtolower;
use function strtr;

use const T_DECLARE;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_OPEN_TAG;
use const T_STRING;
use const T_WHITESPACE;

class LicenseHeaderSniff implements Sniff
{
    /**
     * @var null|string
     */
    public $comment = <<<'EOC'
/**
 * @see       https://github.com/{org}/{repo} for the canonical source repository
 * @copyright https://github.com/{org}/{repo}/blob/master/COPYRIGHT.md Copyright
 * @license   https://github.com/{org}/{repo}/blob/master/LICENSE.md New BSD License
 */
EOC;

    /**
     * @var array
     */
    public $variables = [];

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
        $next = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);

        if ($tokens[$next]['code'] === T_DECLARE) {
            $string = $phpcsFile->findNext(
                T_STRING,
                $tokens[$next]['parenthesis_opener'] + 1,
                $tokens[$next]['parenthesis_closer']
            );

            // If the first statement in the file is strict type declaration.
            if ($string && stripos($tokens[$string]['content'], 'strict_types') !== false) {
                $eos = $phpcsFile->findEndOfStatement($next);
                $next = $phpcsFile->findNext(T_WHITESPACE, $eos + 1, null, true);
            }
        }

        if ($next && $tokens[$next]['code'] === T_DOC_COMMENT_OPEN_TAG) {
            $prev = $phpcsFile->findPrevious(T_WHITESPACE, $next - 1, null, true);
            if ($tokens[$prev]['code'] === T_OPEN_TAG
                && $tokens[$prev]['line'] + 1 !== $tokens[$next]['line']
            ) {
                $error = 'License header must be in next line after opening PHP tag';
                $fix = $phpcsFile->addFixableError($error, $next, 'Line');

                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = $next - 1; $i > $prev; --$i) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }
                    $phpcsFile->fixer->endChangeset();
                }
            }

            $content = $phpcsFile->getTokensAsString($next, $tokens[$next]['comment_closer'] - $next + 1);
            $comment = $this->getComment();

            if ($comment === $content) {
                return $phpcsFile->numTokens + 1;
            }

            if ($this->hasTags($phpcsFile, $next)) {
                $error = 'Invalid doc license header';
                $fix = $phpcsFile->addFixableError($error, $next, 'Invalid');

                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = $next; $i < $tokens[$next]['comment_closer']; ++$i) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }
                    $phpcsFile->fixer->replaceToken($tokens[$next]['comment_closer'], $comment);
                    $phpcsFile->fixer->endChangeset();
                }

                return $phpcsFile->numTokens + 1;
            }
        }

        $error = 'Missing license header';
        $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Missing');

        if ($fix) {
            $phpcsFile->fixer->addContent($stackPtr, $this->getComment() . $phpcsFile->eolChar);
        }

        return $phpcsFile->numTokens + 1;
    }

    private function getComment() : string
    {
        return strtr($this->comment, array_merge($this->getDefaultVariables(), $this->variables));
    }

    private function hasTags(File $phpcsFile, int $comment) : bool
    {
        $tokens = $phpcsFile->getTokens();
        $tags = ['@copyright' => true, '@license' => true];

        foreach ($tokens[$comment]['comment_tags'] ?? [] as $token) {
            $content = strtolower($tokens[$token]['content']);
            unset($tags[$content]);
        }

        return ! $tags;
    }

    private function getDefaultVariables() : array
    {
        return [
            '{org}' => Config::getConfigData('zfcs:org') ?: 'zendframework',
            '{repo}' => Config::getConfigData('zfcs:repo'),
        ];
    }
}

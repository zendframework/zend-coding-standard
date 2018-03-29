<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYRIGHT.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use function preg_match;
use function preg_replace;
use function strtoupper;
use function trim;

use const T_START_HEREDOC;
use const T_START_NOWDOC;

class HeredocSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [
            T_START_HEREDOC,
            T_START_NOWDOC,
        ];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $content = $tokens[$stackPtr]['content'];

        $expected = preg_replace('/<<<\s+/', '<<<', $tokens[$stackPtr]['content']);
        if ($content !== $expected) {
            $error = 'Heredoc start tag cannot contain any whitespaces; found "%s", expected "%s"';
            $data = [
                trim($content),
                trim($expected),
            ];
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Space', $data);

            if ($fix) {
                $phpcsFile->fixer->replaceToken($stackPtr, $expected);
            }
        }

        $expected = strtoupper($content);
        if ($content !== $expected) {
            if (preg_match('/[a-z][A-Z]/', $content)) {
                $error = 'Heredoc tag must be uppercase underscore separated, cannot be camel case';
                $phpcsFile->addError($error, $stackPtr, 'CamelCase');
            } else {
                $error = 'Heredoc tag must be uppercase; found "%s"; expected "%s"';
                $data = [
                    trim($content),
                    trim($expected),
                ];
                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Uppercase', $data);

                if ($fix) {
                    $closer = $tokens[$stackPtr]['scope_closer'];

                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->replaceToken($stackPtr, $expected);
                    $phpcsFile->fixer->replaceToken($closer, strtoupper($tokens[$closer]['content']));
                    $phpcsFile->fixer->endChangeset();
                }
            }
        }
    }
}

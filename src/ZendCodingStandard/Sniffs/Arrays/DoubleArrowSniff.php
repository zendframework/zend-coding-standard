<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYRIGHT.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractArraySniff;
use PHP_CodeSniffer\Util\Common;

use const T_WHITESPACE;

class DoubleArrowSniff extends AbstractArraySniff
{
    /**
     * Processes a single-line array definition.
     *
     * @param File $phpcsFile The current file being checked.
     * @param int $stackPtr The position of the current token
     *     in the stack passed in $tokens.
     * @param int $arrayStart The token that starts the array definition.
     * @param int $arrayEnd The token that ends the array definition.
     * @param array $indices An array of token positions for the array keys,
     *     double arrows, and values.
     */
    protected function processSingleLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $indices)
    {
        foreach ($indices as $data) {
            if (! isset($data['arrow'])) {
                continue;
            }

            $this->checkSpace($phpcsFile, $data);
        }
    }

    /**
     * Processes a multi-line array definition.
     *
     * @param File $phpcsFile The current file being checked.
     * @param int $stackPtr The position of the current token
     *     in the stack passed in $tokens.
     * @param int $arrayStart The token that starts the array definition.
     * @param int $arrayEnd The token that ends the array definition.
     * @param array $indices An array of token positions for the array keys,
     *     double arrows, and values.
     */
    protected function processMultiLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $indices)
    {
        $tokens = $phpcsFile->getTokens();

        foreach ($indices as $data) {
            if (! isset($data['arrow'])) {
                continue;
            }

            $arrow = $tokens[$data['arrow']];
            $value = $tokens[$data['value_start']];

            if ($value['line'] > $arrow['line']) {
                $error = 'Double arrow in array cannot be at the end of the line';
                $fix = $phpcsFile->addFixableError($error, $data['arrow'], 'AtTheEnd');

                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->replaceToken($data['arrow'], '');
                    for ($i = $data['arrow'] - 1; $i > $data['index_end']; --$i) {
                        if ($tokens[$i]['code'] !== T_WHITESPACE) {
                            break;
                        }

                        $phpcsFile->fixer->replaceToken($i, '');
                    }
                    $phpcsFile->fixer->addContentBefore($data['value_start'], '=> ');
                    $phpcsFile->fixer->endChangeset();
                }

                continue;
            }

            $index = $tokens[$data['index_end']];
            if ($index['line'] === $arrow['line']) {
                $this->checkSpace($phpcsFile, $data);
            }
        }
    }

    private function checkSpace(File $phpcsFile, array $element) : void
    {
        $tokens = $phpcsFile->getTokens();

        $space = $tokens[$element['arrow'] - 1];
        if ($space['code'] === T_WHITESPACE && $space['content'] !== ' ') {
            $error = 'Expected 1 space before "=>"; "%s" found';
            $data = [
                Common::prepareForOutput($space['content']),
            ];
            $fix = $phpcsFile->addFixableError($error, $element['arrow'], 'SpaceBefore', $data);

            if ($fix) {
                $phpcsFile->fixer->replaceToken($element['arrow'] - 1, ' ');
            }
        }
    }
}

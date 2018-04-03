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

use function file_exists;
use function touch;

use const T_INLINE_HTML;
use const T_MD_LINE;
use const T_OPEN_TAG;

/**
 * This sniff should run only once to create all missing files.
 * It's hacky solution, because sniffs works only on existing
 * files. So here we try to run the sniff on any file to check
 * for required files, and on fixing we create them.
 */
class CreateSniff implements Sniff
{
    /**
     * @var string[]
     */
    public $supportedTokenizers = [
        'MD',
        'PHP',
    ];

    /**
     * @var string[]
     */
    public $files = [
        'COPYRIGHT.md',
        'LICENSE.md',
    ];

    /**
     * @var bool
     */
    private $run = false;

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [
            T_INLINE_HTML,
            T_MD_LINE,
            T_OPEN_TAG,
        ];
    }

    /**
     * @param int $stackPtr
     * @return int
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->run) {
            return $phpcsFile->numTokens + 1;
        }
        $this->run = true;

        foreach ($this->files as $file) {
            if (! file_exists($file)) {
                $error = 'File %s does not exist';
                $data = [$file];
                $fix = $phpcsFile->addFixableError($error, 0, 'NotExists', $data);

                if ($fix) {
                    touch($file);
                }
            }
        }

        return $phpcsFile->numTokens + 1;
    }
}

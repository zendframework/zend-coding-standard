<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYRIGHT.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\Files;

use DateTime;
use DateTimeZone;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use function array_merge;
use function basename;
use function exec;
use function file_get_contents;
use function filter_var;
use function rtrim;
use function strpos;
use function strstr;
use function strtolower;
use function strtr;
use function substr;
use function trim;
use function ucfirst;

use const FILTER_VALIDATE_URL;
use const T_MD_LINE;

class MdSniff implements Sniff
{
    /**
     * @var string[]
     */
    public $supportedTokenizers = [
        'MD',
    ];

    /**
     * @var string[]
     */
    public $templates = [
        // @phpcs:disable Generic.Files.LineLength.TooLong
        'LICENSE.md' => 'https://raw.githubusercontent.com/zendframework/maintainers/master/template/LICENSE.md',
        'COPYRIGHT.md' => 'Copyright (c) {year}, Zend Technologies USA, Inc.' . "\n",
        'CODE_OF_CONDUCT.md' => 'https://raw.githubusercontent.com/zendframework/maintainers/master/template/docs/CODE_OF_CONDUCT.md',
        'CONTRIBUTING.md' => 'https://raw.githubusercontent.com/zendframework/maintainers/master/template/docs/CONTRIBUTING.md',
        'ISSUE_TEMPLATE.md' => 'https://raw.githubusercontent.com/zendframework/maintainers/master/template/docs/ISSUE_TEMPLATE.md',
        'PULL_REQUEST_TEMPLATE.md' => 'https://raw.githubusercontent.com/zendframework/maintainers/master/template/docs/PULL_REQUEST_TEMPLATE.md',
        'SUPPORT.md' => 'https://raw.githubusercontent.com/zendframework/maintainers/master/template/docs/SUPPORT.md',
        // @phpcs:enable
    ];

    /**
     * @var string[]
     */
    public $variables = [];

    /**
     * @var string
     */
    public $yearTimezone = 'GMT';

    /**
     * @var null|string
     */
    private $yearRange;

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_MD_LINE];
    }

    /**
     * @param int $stackPtr
     * @return int
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $template = $this->getTemplate($phpcsFile->getFilename());
        if ($template === null) {
            $tokens = $phpcsFile->getTokens();

            do {
                $content = $tokens[$stackPtr]['content'];
                $expected = rtrim($content) . (substr($content, -1) === "\n" ? "\n" : '');

                if ($content !== $expected) {
                    $error = 'White characters found at the end of the line';
                    $fix = $phpcsFile->addFixableError($error, $stackPtr, 'WhiteChars');

                    if ($fix) {
                        $phpcsFile->fixer->replaceToken($stackPtr, $expected);
                    }
                }
            } while ($stackPtr = $phpcsFile->findNext(T_MD_LINE, $stackPtr + 1));

            if (trim($tokens[$phpcsFile->numTokens - 1]['content']) !== '') {
                $error = 'Missing empty line at the end of the file';
                $fix = $phpcsFile->addFixableError($error, $phpcsFile->numTokens - 1, 'MissingEmptyLine');

                if ($fix) {
                    $phpcsFile->fixer->addNewline($phpcsFile->numTokens - 1);
                }
            } elseif ($phpcsFile->numTokens > 1 && trim($tokens[$phpcsFile->numTokens - 2]['content']) === '') {
                $error = 'Redundant empty line at the end of the file';
                $fix = $phpcsFile->addFixableError($error, $phpcsFile->numTokens - 1, 'RedundantEmptyLine');

                if ($fix) {
                    $phpcsFile->fixer->replaceToken($phpcsFile->numTokens - 2, '');
                }
            }

            return $phpcsFile->numTokens + 1;
        }

        $content = $phpcsFile->getTokensAsString(0, $phpcsFile->numTokens);

        $newContent = strtr($template, array_merge($this->getDefaultVariables(), $this->variables));

        if ($content !== $newContent) {
            $error = 'Content is outdated; found %s; expected %s';
            $data = [$content, $newContent];
            $code = ucfirst(strtolower(strstr(basename($phpcsFile->getFilename()), '.', true)));
            $fix = $phpcsFile->addFixableError($error, $stackPtr, $code, $data);

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = 0; $i < $phpcsFile->numTokens; ++$i) {
                    $phpcsFile->fixer->replaceToken($i, '');
                }
                $phpcsFile->fixer->addContent(0, $newContent);
                $phpcsFile->fixer->endChangeset();
            }
        }

        return $phpcsFile->numTokens + 1;
    }

    private function getTemplate(string $filename) : ?string
    {
        foreach ($this->templates as $name => $template) {
            if (strpos($filename, '/' . $name) !== false) {
                if (filter_var($template, FILTER_VALIDATE_URL)) {
                    return file_get_contents($template);
                }

                return $template;
            }
        }

        return null;
    }

    private function getDefaultVariables() : array
    {
        return [
            '{category}' => Config::getConfigData('zfcs:category') ?: 'components',
            '{org}' => Config::getConfigData('zfcs:org') ?: 'zendframework',
            '{repo}' => Config::getConfigData('zfcs:repo'),
            '{year}' => $this->getYearRange(),
        ];
    }

    private function getYearRange() : string
    {
        if (! $this->yearRange) {
            $timezone = new DateTimeZone($this->yearTimezone);

            exec('git tag -l --format=\'%(taggerdate)\' | head -n 1', $output, $return);
            $date = new DateTime($return === 0 && isset($output[0]) ? $output[0] : 'now');
            $date->setTimezone($timezone);
            $this->yearRange = $date->format('Y');

            $currentYear = (new DateTime('now', $timezone))->format('Y');
            if ($this->yearRange < $currentYear) {
                $this->yearRange .= '-' . $currentYear;
            }
        }

        return $this->yearRange;
    }
}

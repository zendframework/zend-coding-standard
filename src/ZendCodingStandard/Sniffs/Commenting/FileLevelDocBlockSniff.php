<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYING.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

use Zend\CodingStandard\Utils\LicenseUtils;

class ZendCodingStandard_Sniffs_Commenting_FileLevelDocBlockSniff implements \PHP_CodeSniffer_Sniff
{
    private $repo;

    private $licenseFirstYear;

    private $licenseLastYear;

    private $copyrightChanged;

    private $fix;

    public function __construct()
    {
        $this->licenseFirstYear = gmdate('Y');
        $this->licenseLastYear = gmdate('Y');

        $year = LicenseUtils::getCopyrightFirstYear(LicenseUtils::getCopyrightFile());
        if ($year !== null && $year < $this->licenseFirstYear) {
            $this->licenseFirstYear = $year;
        }

        $year = LicenseUtils::getCopyrightFirstYear(LicenseUtils::getLicenseFile());
        if ($year !== null && $year < $this->licenseFirstYear) {
            $this->licenseFirstYear = $year;
        }

        $content = file_get_contents('composer.json');
        $content = json_decode($content, true);

        $this->repo = $content['name'];
    }

    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[]
     * @see    Tokens.php
     */
    public function register()
    {
        return [T_OPEN_TAG];
    }

    /**
     * Called when one of the token types that this sniff is listening for is
     * found.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The PHP_CodeSniffer file where the
     *                                        token was found.
     * @param int                  $stackPtr  The position in the PHP_CodeSniffer
     *                                        file's token stack where the token
     *                                        was found.
     *
     * @return int Optionally returns a stack pointer. The sniff will not be
     *                  called again on the current file until the returned stack
     *                  pointer is reached. Return (count($tokens) + 1) to skip
     *                  the rest of the file.
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        // Skip license file
        if (in_array(substr($phpcsFile->getFilename(), -10), ['LICENSE.md', 'COPYING.md'])) {
            return ($phpcsFile->numTokens + 1);
        }

        $tokens = $phpcsFile->getTokens();
        $commentStart = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);

        if ($tokens[$commentStart]['code'] === T_COMMENT) {
            $phpcsFile->addError(
                'You must use "/**" style comments for a file-level DocBlock',
                $commentStart,
                'WrongStyle'
            );
            $phpcsFile->recordMetric($stackPtr, 'File has doc comment', 'yes');

            return ($phpcsFile->numTokens + 1);
        }

        if ($commentStart === false || $tokens[$commentStart]['code'] !== T_DOC_COMMENT_OPEN_TAG) {
            $phpcsFile->addError('Missing file-level DocBlock', $stackPtr, 'Missing');
            $phpcsFile->recordMetric($stackPtr, 'File has file-level DocBlock', 'no');

            return ($phpcsFile->numTokens + 1);
        }

        $commentEnd = $tokens[$commentStart]['comment_closer'];

        $nextToken = $phpcsFile->findNext(
            T_WHITESPACE,
            $commentEnd + 1,
            null,
            true
        );

        $ignore = [
            T_CLASS,
            T_INTERFACE,
            T_TRAIT,
            T_FUNCTION,
            T_CLOSURE,
            T_PUBLIC,
            T_PRIVATE,
            T_PROTECTED,
            T_FINAL,
            T_STATIC,
            T_ABSTRACT,
            T_CONST,
            T_PROPERTY,
            T_INCLUDE,
            T_INCLUDE_ONCE,
            T_REQUIRE,
            T_REQUIRE_ONCE,
        ];

        if (in_array($tokens[$nextToken]['code'], $ignore) === true) {
            $phpcsFile->addError('Missing file-level DocBlock', $stackPtr, 'Missing');
            $phpcsFile->recordMetric($stackPtr, 'File has file-level DocBlock', 'no');

            return ($phpcsFile->numTokens + 1);
        }

        $phpcsFile->recordMetric($stackPtr, 'File has file-level DocBlock', 'yes');

        // No blank line between the open tag and the file comment.
        if ($tokens[$commentStart]['line'] > ($tokens[$stackPtr]['line'] + 1)) {
            $error = 'There must be no blank lines before the file-level DocBlock';
            $phpcsFile->addError($error, $stackPtr, 'SpacingAfterOpen');
        }

        // Exactly one blank line after the file comment.
        $next = $phpcsFile->findNext(T_WHITESPACE, ($commentEnd + 1), null, true);
        if ($tokens[$next]['line'] !== ($tokens[$commentEnd]['line'] + 2)) {
            $error = 'There must be exactly one blank line after the file-level DocBlock';
            $phpcsFile->addError($error, $commentEnd, 'SpacingAfterComment');
        }

        // Required tags in correct order.
        $required = [
            '@see'       => true,
            '@copyright' => true,
            '@license'   => true,
        ];

        $foundTags = [];
        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            $name = $tokens[$tag]['content'];
            $isRequired = isset($required[$name]);

            if ($isRequired === true && in_array($name, $foundTags) === true) {
                $error = 'Only one %s tag is allowed in a file-level DocBlock';
                $data = [$name];
                $phpcsFile->addError($error, $tag, 'Duplicate' . ucfirst(substr($name, 1)) . 'Tag', $data);
            }

            $foundTags[] = $name;

            if ($isRequired === false) {
                continue;
            }

            $string = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $tag, $commentEnd);
            if ($string === false || $tokens[$string]['line'] !== $tokens[$tag]['line']) {
                $error = 'Content missing for %s tag in file-level DocBlock';
                $data = [$name];
                $phpcsFile->addError($error, $tag, 'Empty' . ucfirst(substr($name, 1)) . 'Tag', $data);
                continue;
            }

            if ($name === '@see') {
                $expected = sprintf('https://github.com/%s for the canonical source repository', $this->repo);
                if (preg_match('|^' . $expected . '$|', $tokens[$string]['content']) === 0) {
                    $error = 'Expected "%s" for @see tag';
                    $fix = $phpcsFile->addFixableError($error, $tag, 'IncorrectSourceLink', [$expected]);
                    if ($fix === true) {
                        $this->fix = true;
                        $phpcsFile->fixer->replaceToken($string, $expected);
                    }
                }
                continue;
            }

            if ($name === '@copyright') {
                // Grab license date range
                $this->parseCopyrightDate($tokens[$string]['content']);

                $expected = sprintf('https://github.com/%s/blob/master/COPYING.md Copyright', $this->repo);
                if (preg_match('|^' . $expected . '$|', $tokens[$string]['content']) === 0) {
                    $error = 'Expected "%s" for @copyright tag';
                    $fix = $phpcsFile->addFixableError($error, $tag, 'IncorrectCopyrightLink', [$expected]);
                    if ($fix === true) {
                        $this->fix = true;
                        $phpcsFile->fixer->replaceToken($string, $expected);
                    }
                }
                continue;
            }

            if ($name === '@license') {
                $expected = sprintf('https://github.com/%s/blob/master/LICENSE.md New BSD License', $this->repo);
                if (preg_match('|^' . $expected . '$|', $tokens[$string]['content']) === 0) {
                    $error = 'Expected "%s" for @license tag';
                    $fix = $phpcsFile->addFixableError($error, $tag, 'IncorrectLicenseLink', [$expected]);
                    if ($fix === true) {
                        $this->fix = true;
                        $phpcsFile->fixer->replaceToken($string, $expected);
                    }
                }
                continue;
            }
        }

        // Check if the tags are in the correct position.
        $pos = 0;
        foreach ($required as $tag => $true) {
            if (in_array($tag, $foundTags) === false) {
                $error = 'Missing %s tag in file-level DocBlock';
                $data = [$tag];
                $phpcsFile->addError($error, $commentEnd, 'Missing' . ucfirst(substr($tag, 1)) . 'Tag', $data);
            }

            if (isset($foundTags[$pos]) === false) {
                break;
            }

            if ($foundTags[$pos] !== $tag) {
                $error = 'The file-level DocBlock tag in position %s should be the %s tag';
                $data = [
                    ($pos + 1),
                    $tag,
                ];
                $phpcsFile->addWarning(
                    $error,
                    $tokens[$commentStart]['comment_tags'][$pos],
                    ucfirst(substr($tag, 1)) . 'TagOrder',
                    $data
                );
            }

            $pos++;
        }

        if ($this->copyrightChanged === true && $this->fix === true) {
            // Make sure the files exist
            LicenseUtils::createCopyrightFile();
            LicenseUtils::createLicenseFile();

            // Update copyright file
            LicenseUtils::updateCopyright(
                LicenseUtils::getCopyrightFile(),
                $this->licenseFirstYear,
                $this->licenseLastYear
            );

            // Update license file
            LicenseUtils::updateCopyright(
                LicenseUtils::getLicenseFile(),
                $this->licenseFirstYear,
                $this->licenseLastYear
            );

            $this->copyrightChanged = false;
        }

        // Ignore the rest of the file.
        return ($phpcsFile->numTokens + 1);
    }

    private function parseCopyrightDate($string)
    {
        $matches = [];
        preg_match('|(?<start>[\d]{4})(-(?<end>[\d]{4}))?|', $string, $matches);

        $licenseFirstYear = isset($matches['start']) ? $matches['start'] : null;
        $licenseLastYear = isset($matches['end']) ? $matches['end'] : null;
        if ($licenseFirstYear !== null && $this->licenseFirstYear > $licenseFirstYear) {
            $this->licenseFirstYear = $licenseFirstYear;
            $this->copyrightChanged = true;
        }
        if ($licenseLastYear !== null && $this->licenseLastYear < $licenseLastYear) {
            $this->licenseLastYear = $licenseLastYear;
            $this->copyrightChanged = true;
        }
    }
}

<?php
namespace ZendCodingStandard\Tests;

use PHP_CodeSniffer;
use PHP_CodeSniffer_Exception;
use PHP_CodeSniffer_File;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /** @var PHP_CodeSniffer */
    protected $phpcs;

    protected function setUp()
    {
        parent::setUp();

        chdir(__DIR__ . '/../../');

        if (! defined('PHP_CODESNIFFER_IN_TESTS')) {
            define('PHP_CODESNIFFER_IN_TESTS', true);
        }

        $GLOBALS['PHP_CODESNIFFER_SNIFF_CODES'] = [];
        $GLOBALS['PHP_CODESNIFFER_FIXABLE_CODES'] = [];
        $GLOBALS['PHP_CODESNIFFER_CONFIG_DATA']['installed_paths'] = __DIR__ . '/../../';
        $this->phpcs = new PHP_CodeSniffer();
    }

    protected function getTestFiles($testFileBase)
    {
        $testFiles = [];

        $dir = substr($testFileBase, 0, strrpos($testFileBase, DIRECTORY_SEPARATOR));
        $di = new \DirectoryIterator($dir);

        foreach ($di as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $path = $file->getPathname();
            // if (substr($path, 9, strlen($testFileBase)) === $testFileBase)
            if ($path !== $testFileBase . 'php' && substr($path, -5) !== 'fixed') {
                $testFiles[] = $path;
            }
        }

        sort($testFiles);

        return $testFiles;
    }

    final public function testSniff()
    {
        $basename = substr(get_class($this), 0, -8);

        $standardName = substr($basename, 0, strpos($basename, '\\'));

        $parts = explode('\\', $basename);
        $sniffCode = $parts[0] . '.' . $parts[2] . '.' . $parts[3];

        $testFileBase = __DIR__
            . '/../../'
            . str_replace('\\', DIRECTORY_SEPARATOR, $basename) . 'UnitTest.';

        $testFiles = $this->getTestFiles($testFileBase);

        $this->phpcs->initStandard($standardName, [$sniffCode]);
        $this->phpcs->setIgnorePatterns([]);

        $failureMessages = [];
        foreach ($testFiles as $testFile) {
            $filename = basename($testFile);

            try {
                $phpcsFile = $this->phpcs->processFile($testFile);
            } catch (\Exception $e) {
                $this->fail(sprintf(
                    'An unexpected exception has been caught: %s',
                    $e->getMessage()
                ));
            }


            $failures = $this->generateFailureMessages($phpcsFile);
            $failureMessages = array_merge($failureMessages, $failures);

            if ($phpcsFile->getFixableCount() > 0) {
                // Attempt to fix the errors.
                $phpcsFile->fixer->fixFile();
                $fixable = $phpcsFile->getFixableCount();
                if ($fixable) {
                    $failureMessages[] = sprintf(
                        'Failed to fix %d fixable violations in %s',
                        $fixable,
                        $filename
                    );
                }

                // Check for a .fixed file to check for accuracy of fixes.
                $fixedFile = $testFile . '.fixed';
                if (file_exists($fixedFile)) {
                    $diff = $phpcsFile->fixer->generateDiff($fixedFile);
                    if (trim($diff) !== '') {
                        $filename = basename($testFile);
                        $fixedFilename = basename($fixedFile);
                        $failureMessages[] = sprintf(
                            'Fixed version of %s does not match expected version in %s; the diff is' . PHP_EOL . '%s',
                            $filename,
                            $fixedFilename,
                            $diff
                        );
                    }
                }
            }
        }

        if (! empty($failureMessages)) {
            $this->fail(implode(PHP_EOL, $failureMessages));
        }
    }

    public function generateFailureMessages(PHP_CodeSniffer_File $file)
    {
        $testFile = $file->getFilename();

        $foundErrors = $file->getErrors();
        $foundWarnings = $file->getWarnings();
        $expectedErrors = $this->getErrorList(basename($testFile));
        $expectedWarnings = $this->getWarningList(basename($testFile));

        if (! is_array($expectedErrors)) {
            throw new PHP_CodeSniffer_Exception('getErrorList() must return an array');
        }

        if (! is_array($expectedWarnings)) {
            throw new PHP_CodeSniffer_Exception('getWarningList() must return an array');
        }

        /*
            We merge errors and warnings together to make it easier
            to iterate over them and produce the errors string. In this way,
            we can report on errors and warnings in the same line even though
            it's not really structured to allow that.
        */

        $allProblems = [];
        $failureMessages = [];

        foreach ($foundErrors as $line => $lineErrors) {
            foreach ($lineErrors as $column => $errors) {
                if (! isset($allProblems[$line])) {
                    $allProblems[$line] = [
                        'expected_errors' => 0,
                        'expected_warnings' => 0,
                        'found_errors' => [],
                        'found_warnings' => [],
                    ];
                }

                $foundErrorsTemp = [];
                foreach ($allProblems[$line]['found_errors'] as $foundError) {
                    $foundErrorsTemp[] = $foundError;
                }

                $errorsTemp = [];
                foreach ($errors as $foundError) {
                    $errorsTemp[] = $foundError['message'] . ' (' . $foundError['source'] . ')';

                    $source = $foundError['source'];
                    if (! in_array($source, $GLOBALS['PHP_CODESNIFFER_SNIFF_CODES'])) {
                        $GLOBALS['PHP_CODESNIFFER_SNIFF_CODES'][] = $source;
                    }

                    if ($foundError['fixable'] === true
                        && ! in_array($source, $GLOBALS['PHP_CODESNIFFER_FIXABLE_CODES'])
                    ) {
                        $GLOBALS['PHP_CODESNIFFER_FIXABLE_CODES'][] = $source;
                    }
                }

                $allProblems[$line]['found_errors'] = array_merge($foundErrorsTemp, $errorsTemp);
            }

            if (isset($expectedErrors[$line])) {
                $allProblems[$line]['expected_errors'] = $expectedErrors[$line];
            } else {
                $allProblems[$line]['expected_errors'] = 0;
            }

            unset($expectedErrors[$line]);
        }

        foreach ($expectedErrors as $line => $numErrors) {
            if (! isset($allProblems[$line])) {
                $allProblems[$line] = [
                    'expected_errors' => 0,
                    'expected_warnings' => 0,
                    'found_errors' => [],
                    'found_warnings' => [],
                ];
            }

            $allProblems[$line]['expected_errors'] = $numErrors;
        }

        foreach ($foundWarnings as $line => $lineWarnings) {
            foreach ($lineWarnings as $column => $warnings) {
                if (! isset($allProblems[$line])) {
                    $allProblems[$line] = [
                        'expected_errors' => 0,
                        'expected_warnings' => 0,
                        'found_errors' => [],
                        'found_warnings' => [],
                    ];
                }

                $foundWarningsTemp = [];
                foreach ($allProblems[$line]['found_warnings'] as $foundWarning) {
                    $foundWarningsTemp[] = $foundWarning;
                }

                $warningsTemp = [];
                foreach ($warnings as $warning) {
                    $warningsTemp[] = $warning['message'] . ' (' . $warning['source'] . ')';
                }

                $allProblems[$line]['found_warnings'] = array_merge($foundWarningsTemp, $warningsTemp);
            }

            if (isset($expectedWarnings[$line])) {
                $allProblems[$line]['expected_warnings'] = $expectedWarnings[$line];
            } else {
                $allProblems[$line]['expected_warnings'] = 0;
            }

            unset($expectedWarnings[$line]);
        }

        foreach ($expectedWarnings as $line => $numWarnings) {
            if (! isset($allProblems[$line])) {
                $allProblems[$line] = [
                    'expected_errors' => 0,
                    'expected_warnings' => 0,
                    'found_errors' => [],
                    'found_warnings' => [],
                ];
            }

            $allProblems[$line]['expected_warnings'] = $numWarnings;
        }

        // Order the messages by line number.
        ksort($allProblems);

        foreach ($allProblems as $line => $problems) {
            $numErrors = count($problems['found_errors']);
            $numWarnings = count($problems['found_warnings']);
            $expectedErrors = $problems['expected_errors'];
            $expectedWarnings = $problems['expected_warnings'];

            $errors = '';
            $foundString = '';

            if ($expectedErrors !== $numErrors || $expectedWarnings !== $numWarnings) {
                $lineMessage = sprintf('[LINE %s]', $line);
                $expectedMessage = 'Expected ';
                $foundMessage = sprintf('in %s but found ', basename($testFile));

                if ($expectedErrors !== $numErrors) {
                    $expectedMessage .= sprintf('%s error(s)', $expectedErrors);
                    $foundMessage .= sprintf('%s error(s)', $numErrors);
                    if ($numErrors !== 0) {
                        $foundString .= 'error(s)';
                        $errors .= implode(PHP_EOL . ' -> ', $problems['found_errors']);
                    }

                    if ($expectedWarnings !== $numWarnings) {
                        $expectedMessage .= ' and ';
                        $foundMessage .= ' and ';
                        if ($numWarnings !== 0) {
                            if ($foundString !== '') {
                                $foundString .= ' and ';
                            }
                        }
                    }
                }

                if ($expectedWarnings !== $numWarnings) {
                    $expectedMessage .= sprintf('%s warning(s)', $expectedWarnings);
                    $foundMessage .= sprintf('%s warning(s)', $numWarnings);
                    if ($numWarnings !== 0) {
                        $foundString .= 'warning(s)';
                        if (! empty($errors)) {
                            $errors .= PHP_EOL . ' -> ';
                        }

                        $errors .= implode(PHP_EOL . ' -> ', $problems['found_warnings']);
                    }
                }

                $fullMessage = sprintf('%s %s %s.', $lineMessage, $expectedMessage, $foundMessage);
                if ($errors !== '') {
                    $fullMessage .= sprintf(' The %s found were:' . PHP_EOL . ' -> %s', $foundString, $errors);
                }

                $failureMessages[] = $fullMessage;
            }
        }

        return $failureMessages;
    }

    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @return array(int => int)
     */
    abstract protected function getErrorList();

    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @return array(int => int)
     */
    abstract protected function getWarningList();
}

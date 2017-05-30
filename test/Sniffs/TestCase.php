<?php
namespace ZendCodingStandardTest\Sniffs;

use DirectoryIterator;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\LocalFile;
use PHP_CodeSniffer\Util\Common;
use PHP_CodeSniffer\Util\Tokens;
use ZendCodingStandardTest\Ruleset;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * The path to the standard's main directory.
     *
     * @var string
     */
    public $standardsDir = __DIR__ . '/../../src/';

    /**
     * The path to the standard's test directory.
     *
     * @var string
     */
    public $testsDir = __DIR__ . '/../../test/Sniffs/';

    protected function setUp()
    {
        // Initialize tokens constants
        new Tokens();
    }

    /**
     * Get a list of all test files to check.
     *
     * These will have the same base as the sniff name but different extensions.
     * We ignore the .php file as it is the class.
     *
     * @param string $testFileBase The base path that the unit tests files will have.
     * @return string[]
     */
    protected function getTestFiles($testFileBase)
    {
        $testFiles = [];

        $dir = substr($testFileBase, 0, strrpos($testFileBase, DIRECTORY_SEPARATOR));
        $di = new DirectoryIterator($dir);

        foreach ($di as $file) {
            $path = $file->getPathname();
            if (strpos($path, $testFileBase) === 0) {
                if ($path !== $testFileBase . 'php'
                    && substr($path, -5) !== 'fixed'
                ) {
                    $testFiles[] = $path;
                }
            }
        }

        // Put them in order.
        sort($testFiles);

        return $testFiles;
    }

    /**
     * Should this test be skipped for some reason.
     *
     * @return bool
     */
    protected function shouldSkipTest()
    {
        return false;
    }

    /**
     * Tests the extending classes Sniff class.
     *
     * @return void
     */
    final public function testSniff()
    {
        // Skip this test if we can't run in this environment.
        if ($this->shouldSkipTest()) {
            $this->markTestSkipped();
        }

        $sniffCode = Common::getSniffCode(get_class($this));
        $sniffCode = str_replace('Test.', '.', $sniffCode);
        list($standardName, $categoryName, $sniffName) = explode('.', $sniffCode);

        $testFileBase = $this->testsDir . $categoryName . DIRECTORY_SEPARATOR . $sniffName . 'UnitTest.';

        // Get a list of all test files to check.
        $testFiles = $this->getTestFiles($testFileBase);

        $config = new Config();
        $config->cache = false;
        $config->standards = [$standardName];
        $config->sniffs = [$sniffCode];
        $config->ignored = [];

        $ruleset = new Ruleset($config);

        $failureMessages = [];
        foreach ($testFiles as $testFile) {
            $filename = basename($testFile);
            $oldConfig = $config->getSettings();

            try {
                $this->setCliValues($filename, $config);
                $phpcsFile = new LocalFile($testFile, $ruleset, $config);
                $phpcsFile->process();
            } catch (RuntimeException $e) {
                $this->fail(sprintf('An unexpected exception has been caught: %s', $e->getMessage()));
            }

            $failures = $this->generateFailureMessages($phpcsFile);
            $failureMessages = array_merge($failureMessages, $failures);

            if ($phpcsFile->getFixableCount() > 0) {
                // Attempt to fix the errors.
                $phpcsFile->fixer->fixFile();
                $fixable = $phpcsFile->getFixableCount();
                if ($fixable > 0) {
                    $failureMessages[] = sprintf('Failed to fix %d fixable violations in %s', $fixable, $filename);
                }

                // Check for a .fixed file to check for accuracy of fixes.
                $fixedFile = $testFile . '.fixed';
                if (file_exists($fixedFile)) {
                    $diff = $phpcsFile->fixer->generateDiff($fixedFile);
                    if (trim($diff) !== '') {
                        $filename = basename($testFile);
                        $fixedFilename = basename($fixedFile);
                        $failureMessages[] = sprintf(
                            'Fixed version of %s does not match expected version in %s; the diff is%s%s',
                            $filename,
                            $fixedFilename,
                            PHP_EOL,
                            $diff
                        );
                    }
                }
            }

            // Restore the config.
            $config->setSettings($oldConfig);
        }

        if ($failureMessages) {
            $this->fail(implode(PHP_EOL, $failureMessages));
        }
    }

    /**
     * Generate a list of test failures for a given sniffed file.
     *
     * @param LocalFile $file The file being tested.
     * @return array
     * @throws RuntimeException
     */
    public function generateFailureMessages(LocalFile $file)
    {
        $testFile = $file->getFilename();

        $foundErrors = $file->getErrors();
        $foundWarnings = $file->getWarnings();
        $expectedErrors = $this->getErrorList(basename($testFile));
        $expectedWarnings = $this->getWarningList(basename($testFile));

        if (! is_array($expectedErrors)) {
            throw new RuntimeException('getErrorList() must return an array');
        }

        if (! is_array($expectedWarnings)) {
            throw new RuntimeException('getWarningList() must return an array');
        }

        // We merge errors and warnings together to make it easier
        // to iterate over them and produce the errors string. In this way,
        // we can report on errors and warnings in the same line even though
        // it's not really structured to allow that.

        $allProblems = [];
        $failureMessages = [];

        foreach ($foundErrors as $line => $lineErrors) {
            foreach ($lineErrors as $column => $errors) {
                if (! isset($allProblems[$line])) {
                    $allProblems[$line] = [
                        'expected_errors'   => 0,
                        'expected_warnings' => 0,
                        'found_errors'      => [],
                        'found_warnings'    => [],
                    ];
                }

                $foundErrorsTemp = [];
                foreach ($allProblems[$line]['found_errors'] as $foundError) {
                    $foundErrorsTemp[] = $foundError;
                }

                $errorsTemp = [];
                foreach ($errors as $foundError) {
                    $errorsTemp[] = $foundError['message'] . ' (' . $foundError['source'] . ')';
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
                    'expected_errors'   => 0,
                    'expected_warnings' => 0,
                    'found_errors'      => [],
                    'found_warnings'    => [],
                ];
            }

            $allProblems[$line]['expected_errors'] = $numErrors;
        }

        foreach ($foundWarnings as $line => $lineWarnings) {
            foreach ($lineWarnings as $column => $warnings) {
                if (! isset($allProblems[$line])) {
                    $allProblems[$line] = [
                        'expected_errors'   => 0,
                        'expected_warnings' => 0,
                        'found_errors'      => [],
                        'found_warnings'    => [],
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
                    'expected_errors'   => 0,
                    'expected_warnings' => 0,
                    'found_errors'      => [],
                    'found_warnings'    => [],
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
                $lineMessage = '[LINE ' . $line . ']';
                $expectedMessage = 'Expected ';
                $foundMessage = 'in ' . basename($testFile) . ' but found ';

                if ($expectedErrors !== $numErrors) {
                    $expectedMessage .= $expectedErrors . ' error(s)';
                    $foundMessage .= $numErrors . ' error(s)';
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
                    $expectedMessage .= $expectedWarnings . ' warning(s)';
                    $foundMessage .= $numWarnings . ' warning(s)';
                    if ($numWarnings !== 0) {
                        $foundString .= 'warning(s)';
                        if ($errors) {
                            $errors .= PHP_EOL . ' -> ';
                        }

                        $errors .= implode(PHP_EOL . ' -> ', $problems['found_warnings']);
                    }
                }

                $fullMessage = sprintf('%s %s %s.', $lineMessage, $expectedMessage, $foundMessage);
                if ($errors !== '') {
                    $fullMessage .= sprintf(' The %s found were:%s -> %s', $foundString, PHP_EOL, $errors);
                }

                $failureMessages[] = $fullMessage;
            }
        }

        return $failureMessages;
    }

    /**
     * Get a list of CLI values to set before the file is tested.
     *
     * @param string $filename The name of the file being tested.
     * @param Config $config The config data for the run.
     * @return array
     */
    public function setCliValues($filename, Config $config)
    {
        return [];
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

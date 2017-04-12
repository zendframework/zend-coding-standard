<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYING.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\CodingStandard;

use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\TestCase;
use PHPUnit\Util\InvalidArgumentHelper;

class SniffTestCase extends TestCase
{
    public function processAsset($asset)
    {
        $phpcs = new \PHP_CodeSniffer();

        $standard = 'src/ZendCodingStandard/ruleset.xml';
        $options = array_merge($phpcs->cli->getDefaults(), [
            'encoding'    => 'utf-8',
            'files'       => [$asset],
            'standard'    => $standard,
            'showSources' => true,
        ]);

        $reflection = new \ReflectionProperty($phpcs->cli, 'values');
        $reflection->setAccessible(true);
        $reflection->setValue($phpcs->cli, $options);

        $phpcs->initStandard($standard, ['ZendCodingStandard.Commenting.FileLevelDocBlock']);
        $phpcs->setIgnorePatterns([]);

        return $phpcs->processFile($asset);
    }

    public function assertErrorCount($expectedCount, \PHP_CodeSniffer_File $file)
    {
        if (! \is_int($expectedCount)) {
            throw InvalidArgumentHelper::factory(1, 'integer');
        }

        $message = sprintf(
            'Failed asserting that "%s" has %d violations.',
            str_replace(__DIR__, 'test', $file->getFilename()),
            $expectedCount
        );

        static::assertThat(
            $file->getErrorCount(),
            new IsEqual($expectedCount),
            $message
        );
    }

    public function assertHasError($expectedError, \PHP_CodeSniffer_File $file)
    {
        foreach ($file->getErrors() as $line => $lineErrors) {
            foreach ($lineErrors as $column => $errors) {
                foreach ($errors as $error) {
                    if (isset($error['source']) && $error['source'] === $expectedError) {
                        static::assertThat(true, static::isTrue());

                        return;
                    }
                }
            }
        }

        $message = sprintf(
            'Failed asserting that "%s" has "%s" error.',
            str_replace(__DIR__, 'test', $file->getFilename()),
            $expectedError
        );

        static::assertThat(false, static::isTrue(), $message);
    }

    public function assertWarningCount($expectedCount, \PHP_CodeSniffer_File $file)
    {
        if (! \is_int($expectedCount)) {
            throw InvalidArgumentHelper::factory(1, 'integer');
        }

        $message = sprintf(
            'Failed asserting that "%s" has %d warnings.',
            str_replace(__DIR__, 'test', $file->getFilename()),
            $expectedCount
        );

        static::assertThat(
            $file->getWarningCount(),
            new IsEqual($expectedCount),
            $message
        );
    }

    public function assertHasWarning($expectedError, \PHP_CodeSniffer_File $file)
    {
        foreach ($file->getWarnings() as $line => $lineErrors) {
            foreach ($lineErrors as $column => $errors) {
                foreach ($errors as $error) {
                    if (isset($error['source']) && $error['source'] === $expectedError) {
                        static::assertThat(true, static::isTrue());

                        return;
                    }
                }
            }
        }

        $message = sprintf(
            'Failed asserting that "%s" has "%s" warning.',
            str_replace(__DIR__, 'test', $file->getFilename()),
            $expectedError
        );

        static::assertThat(false, static::isTrue(), $message);
    }

    public function assertAssetCanBeFixed($fixed, \PHP_CodeSniffer_File $file)
    {
        if ($fixed === null) {
            $message = sprintf(
                'Failed asserting that "%s" has no fixable violations.',
                str_replace(__DIR__, 'test', $file->getFilename())
            );
            $this->assertEquals(0, $file->getFixableCount(), $message);

            return;
        }

        // Try to fix the file
        $file->fixer->fixFile();
        $message = sprintf(
            'Failed to fix %d fixable violations in "%s".',
            $file->getFixableCount(),
            str_replace(__DIR__, 'test', $file->getFilename())
        );
        $this->assertEquals(0, $file->getFixableCount(), $message);

        // Validate fixes
        $message = sprintf(
            'Failed asserting that "%s" has all fixable violations fixed.',
            str_replace(__DIR__, 'test', $file->getFilename())
        );
        $this->assertEquals('', trim($file->fixer->generateDiff($fixed)), $message);
    }
}

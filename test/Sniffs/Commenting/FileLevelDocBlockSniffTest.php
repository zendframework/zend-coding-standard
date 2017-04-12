<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYING.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\CodingStandard\Sniffs\Commenting;

use ZendTest\CodingStandard\SniffTestCase;

class FileLevelDocBlockSniffTest extends SniffTestCase
{
    /**
     * @dataProvider assetsProvider
     */
    public function testAssets($asset, $fixed, $errorCount, array $errors, $warningCount, array $warnings)
    {
        $result = $this->processAsset($asset);

        $this->assertErrorCount($errorCount, $result);
        $this->assertWarningCount($warningCount, $result);

        foreach ($errors as $error) {
            $this->assertHasError($error, $result);
        }

        foreach ($warnings as $warning) {
            $this->assertHasWarning($warning, $result);
        }

        $this->assertAssetCanBeFixed($fixed, $result);
    }

    public function assetsProvider()
    {
        return [
            'FileLevelDocBlockPass' => [
                'asset'        => __DIR__ . '/Assets/FileLevelDocBlockPass.php',
                'fixed'        => null,
                'errorCount'   => 0,
                'errors'       => [],
                'warningCount' => 0,
                'warnings'     => [],
            ],

            'FileLevelDocBlockMissing' => [
                'asset'        => __DIR__ . '/Assets/FileLevelDocBlockMissing.php',
                'fixed'        => null,
                'errorCount'   => 1,
                'errors'       => [
                    'ZendCodingStandard.Commenting.FileLevelDocBlock.Missing',
                ],
                'warningCount' => 0,
                'warnings'     => [],
            ],

            'FileLevelDocBlockSpacingAfterOpen' => [
                'asset'        => __DIR__ . '/Assets/FileLevelDocBlockSpacingAfterOpen.php',
                'fixed'        => null,
                'errorCount'   => 1,
                'errors'       => [
                    'ZendCodingStandard.Commenting.FileLevelDocBlock.SpacingAfterOpen',
                ],
                'warningCount' => 0,
                'warnings'     => [],
            ],

            'FileLevelDocBlockIncorrectSourceLink' => [
                'asset'        => __DIR__ . '/Assets/FileLevelDocBlockIncorrectSourceLink.php',
                'fixed'        => __DIR__ . '/Assets/FileLevelDocBlockIncorrectSourceLink.fixed.php',
                'errorCount'   => 1,
                'errors'       => [
                    'ZendCodingStandard.Commenting.FileLevelDocBlock.IncorrectSourceLink',
                ],
                'warningCount' => 0,
                'warnings'     => [],
            ],

            'FileLevelDocBlockIncorrectCopyrightLink' => [
                'asset'        => __DIR__ . '/Assets/FileLevelDocBlockIncorrectCopyrightLink.php',
                'fixed'        => __DIR__ . '/Assets/FileLevelDocBlockIncorrectCopyrightLink.fixed.php',
                'errorCount'   => 1,
                'errors'       => [
                    'ZendCodingStandard.Commenting.FileLevelDocBlock.IncorrectCopyrightLink',
                ],
                'warningCount' => 0,
                'warnings'     => [],
            ],

            'FileLevelDocBlockIncorrectLicenseLink' => [
                'asset'        => __DIR__ . '/Assets/FileLevelDocBlockIncorrectLicenseLink.php',
                'fixed'        => __DIR__ . '/Assets/FileLevelDocBlockIncorrectLicenseLink.fixed.php',
                'errorCount'   => 1,
                'errors'       => [
                    'ZendCodingStandard.Commenting.FileLevelDocBlock.IncorrectLicenseLink',
                ],
                'warningCount' => 0,
                'warnings'     => [],
            ],

            'FileLevelDocBlockEmptyTags' => [
                'asset'        => __DIR__ . '/Assets/FileLevelDocBlockEmptyTags.php',
                'fixed'        => null,
                'errorCount'   => 3,
                'errors'       => [
                    'ZendCodingStandard.Commenting.FileLevelDocBlock.EmptySeeTag',
                    'ZendCodingStandard.Commenting.FileLevelDocBlock.EmptyCopyrightTag',
                    'ZendCodingStandard.Commenting.FileLevelDocBlock.EmptyLicenseTag',
                ],
                'warningCount' => 0,
                'warnings'     => [],
            ],
        ];
    }
}

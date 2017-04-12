<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYING.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\CodingStandard\Util;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use Zend\CodingStandard\Utils\LicenseUtils;
use SplFileInfo;

class LicenseUtilTest extends TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $root;

    public function setUp()
    {
        $this->root = vfsStream::setup('tmp');
    }

    public function testCopyrightFileIsCreated()
    {
        LicenseUtils::createCopyrightFile(
            new SplFileInfo($this->root->url() . '/COPYING.tmp.md')
        );

        $this->assertTrue($this->root->hasChild('COPYING.tmp.md'));
        $this->assertEquals(
            sprintf(LicenseUtils::$copyright, gmdate('Y')),
            $this->root->getChild('COPYING.tmp.md')->getContent()
        );
    }

    public function testLicenseFileIsCreated()
    {
        LicenseUtils::createLicenseFile(
            new SplFileInfo($this->root->url() . '/LICENSE.tmp.md')
        );

        $this->assertTrue($this->root->hasChild('LICENSE.tmp.md'));
        $this->assertEquals(
            sprintf(LicenseUtils::$license, gmdate('Y')),
            $this->root->getChild('LICENSE.tmp.md')->getContent()
        );
    }
}

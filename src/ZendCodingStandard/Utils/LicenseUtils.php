<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYING.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

namespace Zend\CodingStandard\Utils;

use SplFileInfo;

class LicenseUtils
{
    public static $copyrightLine = 'Copyright (c) %s, Zend Technologies USA, Inc.';

    public static $copyright = <<<EOF
Copyright (c) %s, Zend Technologies USA, Inc.

All rights reserved.

EOF;

    public static $license = <<<EOF
Copyright (c) %s, Zend Technologies USA, Inc.

All rights reserved.

Redistribution and use in source and binary forms, with or without modification,
are permitted provided that the following conditions are met:

- Redistributions of source code must retain the above copyright notice,
  this list of conditions and the following disclaimer.

- Redistributions in binary form must reproduce the above copyright notice,
  this list of conditions and the following disclaimer in the documentation
  and/or other materials provided with the distribution.

- Neither the name of Zend Technologies USA, Inc. nor the names of its
  contributors may be used to endorse or promote products derived from this
  software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

EOF;

    public static function getCopyrightFile()
    {
        return new SplFileInfo('COPYING.md');
    }

    public static function createCopyrightFile(SplFileInfo $file = null)
    {
        if ($file === null) {
            $file = self::getCopyrightFile();
        }

        if ($file->getRealPath()) {
            // File already exists
            return;
        }

        // Create new file
        $file = $file->openFile('x');
        $file->fwrite(sprintf(self::$copyright, gmdate('Y')));
    }

    public static function getLicenseFile()
    {
        return new SplFileInfo('LICENSE.md');
    }

    public static function createLicenseFile(SplFileInfo $file = null)
    {
        if ($file === null) {
            $file = self::getLicenseFile();
        }

        if ($file->getRealPath()) {
            // File already exists
            return;
        }

        // Create new file
        $file = $file->openFile('x');
        $file->fwrite(sprintf(self::$license, gmdate('Y')));
    }

    public static function updateCopyright(SplFileInfo $file, $firstYear, $lastYear = null)
    {
        if ($lastYear === null) {
            $lastYear = gmdate('Y');
        }

        $copyrightDateRange = $lastYear;
        if ($firstYear !== $lastYear) {
            $copyrightDateRange = sprintf('%s-%s', $firstYear, $lastYear);
        }

        // Update copyright line; first line in the file
        $content = file($file->getRealPath());
        $content[0] = sprintf(self::$copyrightLine . PHP_EOL, $copyrightDateRange);
        file_put_contents($file->getRealPath(), $content);
    }

    public static function getCopyrightFirstYear(SplFileInfo $file)
    {
        if (! $file->getRealPath()) {
            return null;
        }

        $content = file($file->getRealPath());
        $matches = [];
        preg_match('|(?<start>[\d]{4})(-(?<end>[\d]{4}))?|', $content[0], $matches);

        $licenseFirstYear = isset($matches['start']) ? $matches['start'] : null;
        $licenseLastYear = isset($matches['end']) ? $matches['end'] : null;

        return $licenseFirstYear;
    }

    public static function getCopyrightDate(SplFileInfo $file)
    {
        if (! $file->getRealPath()) {
            return [null, null];
        }

        $content = file($file->getRealPath());
        $matches = [];
        preg_match('|(?<start>[\d]{4})(-(?<end>[\d]{4}))?|', $content[0], $matches);

        $licenseFirstYear = isset($matches['start']) ? $matches['start'] : null;
        $licenseLastYear = isset($matches['end']) ? $matches['end'] : null;

        return [$licenseFirstYear, $licenseLastYear];
    }
}

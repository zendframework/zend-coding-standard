<?php
namespace ZendCodingStandardTest;

use function str_replace;

class Ruleset extends \PHP_CodeSniffer\Ruleset
{
    /**
     * @param string[] $files
     * @param bool[] $restrictions
     * @param bool[] $exclusions
     */
    public function registerSniffs($files, $restrictions, $exclusions)
    {
        foreach ($restrictions as $className => $bool) {
            $newClassName = str_replace('php_codesniffer\\standards\\', '', $className);
            unset($restrictions[$className]);
            $restrictions[$newClassName] = $bool;
        }

        parent::registerSniffs($files, $restrictions, $exclusions);
    }
}

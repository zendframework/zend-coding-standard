<?php
namespace ZendCodingStandard\Sniffs\PHP;

use Generic_Sniffs_PHP_LowerCaseKeywordSniff;

class LowerCaseKeywordSniff extends Generic_Sniffs_PHP_LowerCaseKeywordSniff
{
    public function register()
    {
        return array_merge(
            parent::register(),
            [
                T_CLOSURE,
                T_PARENT,
                T_SELF,
                T_YIELD,
            ]
        );
    }
}

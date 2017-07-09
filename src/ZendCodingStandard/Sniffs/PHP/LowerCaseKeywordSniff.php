<?php
namespace ZendCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseKeywordSniff as GenericLowerCaseKeywordSniff;

use function array_merge;

class LowerCaseKeywordSniff extends GenericLowerCaseKeywordSniff
{
    /**
     * @inheritDoc
     */
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

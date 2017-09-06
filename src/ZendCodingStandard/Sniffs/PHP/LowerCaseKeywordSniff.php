<?php
namespace ZendCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseKeywordSniff as GenericLowerCaseKeywordSniff;

use function array_merge;

use const T_CLOSURE;
use const T_PARENT;
use const T_SELF;
use const T_YIELD;

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

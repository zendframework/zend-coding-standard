<?php

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractScopeSniff;
use PHP_CodeSniffer\Util\Tokens;

use function in_array;

use const T_CONST;

class ConstVisibilitySniff extends AbstractScopeSniff
{
    public function __construct()
    {
        $scopeTokens = Tokens::$ooScopeTokens;
        $listen = [T_CONST];

        parent::__construct($scopeTokens, $listen, true);
    }

    /**
     * @param int $stackPtr
     * @param int $currScope
     */
    protected function processTokenWithinScope(File $phpcsFile, $stackPtr, $currScope) : void
    {
        $tokens = $phpcsFile->getTokens();
        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $stackPtr - 1, null, true);

        if (! in_array($tokens[$prev]['code'], Tokens::$scopeModifiers, true)) {
            $error = 'Missing constant visibility';
            $phpcsFile->addError($error, $stackPtr, 'MissingVisibility');
        }
    }

    /**
     * @param int $stackPtr
     */
    protected function processTokenOutsideScope(File $phpcsFile, $stackPtr) : void
    {
        // do not process constant outside the scope
    }
}

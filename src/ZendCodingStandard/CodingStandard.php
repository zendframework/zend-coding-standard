<?php
namespace ZendCodingStandard;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Common;
use PHP_CodeSniffer\Util\Tokens;

class CodingStandard
{
    public static $allowedTypes = [
        'array',
        'bool',
        'float',
        'int',
        'mixed',
        'object',
        'string',
        'resource',
        'callable',
    ];

    /**
     * Returns a valid variable type for param/var tag.
     *
     * If type is not one of the standard type, it must be a custom type.
     * Returns the correct type name suggestion if type name is invalid.
     *
     * @param string $varType The variable type to process.
     * @return string
     */
    public static function suggestType($varType)
    {
        $lowerVarType = strtolower($varType);
        switch ($lowerVarType) {
            case 'bool':
            case 'boolean':
                return 'bool';
            case 'int':
            case 'integer':
                return 'int';
            case 'integer[]':
                return 'int[]';
            case 'boolean[]':
                return 'bool[]';
        }

        if (strpos($lowerVarType, 'array(') !== false) {
            // Valid array declaration:
            // array, array(type), array(type1 => type2).
            $matches = [];
            $pattern = '/^array\(\s*([^\s^=^>]*)(\s*=>\s*(.*))?\s*\)/i';
            if (preg_match($pattern, $varType, $matches) !== 0) {
                $type1 = '';
                if (isset($matches[1])) {
                    $type1 = $matches[1];
                }

                $type2 = '';
                if (isset($matches[3])) {
                    $type2 = $matches[3];
                }

                $type1 = self::suggestType($type1);
                $type2 = self::suggestType($type2);
                if ($type2 !== '') {
                    $type2 = ' => ' . $type2;
                }

                if ($type1 || $type2) {
                    return sprintf('array(%s%s)', $type1, $type2);
                }
            }

            return 'array';
        }

        return Common::suggestType($varType);
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return bool
     */
    public static function isTraitUse(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Ignore USE keywords inside closures.
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true);
        if ($tokens[$next]['code'] === T_OPEN_PARENTHESIS) {
            return false;
        }

        // Ignore global USE keywords.
        if (! $phpcsFile->hasCondition($stackPtr, [T_CLASS, T_TRAIT, T_ANON_CLASS])) {
            return false;
        }

        return true;
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return bool
     */
    public static function isGlobalUse(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Ignore USE keywords inside closures.
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true);
        if ($tokens[$next]['code'] === T_OPEN_PARENTHESIS) {
            return false;
        }

        // Ignore USE keywords for traits.
        if ($phpcsFile->hasCondition($stackPtr, [T_CLASS, T_TRAIT, T_ANON_CLASS])) {
            return false;
        }

        return true;
    }
}

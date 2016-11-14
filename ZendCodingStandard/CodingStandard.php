<?php
namespace ZendCodingStandard;

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
                if (isset($matches[1]) === true) {
                    $type1 = $matches[1];
                }

                $type2 = '';
                if (isset($matches[3]) === true) {
                    $type2 = $matches[3];
                }

                $type1 = self::suggestType($type1);
                $type2 = self::suggestType($type2);
                if ($type2 !== '') {
                    $type2 = ' => ' . $type2;
                }

                return sprintf('array(%s%s)', $type1, $type2);
            }

            return 'array';
        }

        return \PHP_CodeSniffer::suggestType($varType);
    }
}

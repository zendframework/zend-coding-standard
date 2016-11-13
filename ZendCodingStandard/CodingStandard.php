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
        switch (strtolower($varType)) {
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

        return \PHP_CodeSniffer::suggestType($varType);
    }
}

<?php
namespace ZendCodingStandard\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use ZendCodingStandard\Helper\Methods;

use function array_filter;
use function array_intersect;
use function array_merge;
use function array_udiff;
use function array_unique;
use function count;
use function current;
use function explode;
use function implode;
use function in_array;
use function preg_grep;
use function preg_match;
use function preg_replace;
use function preg_split;
use function sprintf;
use function strcasecmp;
use function strpos;
use function strtolower;
use function strtr;
use function trim;
use function ucfirst;
use function usort;

use const T_ANON_CLASS;
use const T_ARRAY;
use const T_ARRAY_CAST;
use const T_BOOL_CAST;
use const T_BOOLEAN_NOT;
use const T_CLOSURE;
use const T_COLON;
use const T_CONSTANT_ENCAPSED_STRING;
use const T_DIR;
use const T_DNUMBER;
use const T_DOC_COMMENT_STRING;
use const T_DOUBLE_CAST;
use const T_DOUBLE_QUOTED_STRING;
use const T_FALSE;
use const T_FILE;
use const T_FUNCTION;
use const T_INLINE_THEN;
use const T_INT_CAST;
use const T_LNUMBER;
use const T_NEW;
use const T_NS_SEPARATOR;
use const T_NULL;
use const T_OBJECT_CAST;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_PARENTHESIS;
use const T_OPEN_SHORT_ARRAY;
use const T_OPEN_SQUARE_BRACKET;
use const T_PARENT;
use const T_RETURN;
use const T_RETURN_TYPE;
use const T_SELF;
use const T_SEMICOLON;
use const T_STATIC;
use const T_STRING;
use const T_STRING_CAST;
use const T_TRUE;
use const T_VARIABLE;
use const T_YIELD;
use const T_YIELD_FROM;

class ReturnTypeSniff implements Sniff
{
    use Methods;

    private $returnDoc;
    private $returnDocTypes = [];
    private $returnDocValue;
    private $returnDocDescription;
    private $returnDocIsValid = true;

    private $returnType;
    private $returnTypeValue;
    private $returnTypeIsValid = true;

    /**
     * @return int[]
     */
    public function register()
    {
        return [T_FUNCTION];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $this->initScope($phpcsFile, $stackPtr);

        $this->returnDoc = null;
        $this->returnDocTypes = [];
        $this->returnDocValue = null;
        $this->returnDocDescription = null;
        $this->returnDocIsValid = true;

        $this->returnType = null;
        $this->returnTypeValue = null;
        $this->returnTypeIsValid = true;

        if ($commentStart = $this->getCommentStart($phpcsFile, $stackPtr)) {
            $this->processReturnDoc($phpcsFile, $commentStart);
        }
        $this->processReturnType($phpcsFile, $stackPtr);
        $this->processReturnStatements($phpcsFile, $stackPtr);
    }

    /**
     * @param int $stackPtr
     * @return bool|int
     */
    private function getReturnType(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['scope_opener'])) {
            $to = $tokens[$stackPtr]['scope_opener'];
        } else {
            $to = $phpcsFile->findEndOfStatement($stackPtr, [T_COLON]);
        }

        return $phpcsFile->findNext(T_RETURN_TYPE, $stackPtr + 1, $to);
    }

    /**
     * @param int $commentStart
     */
    private function processReturnDoc(File $phpcsFile, $commentStart)
    {
        $tokens = $phpcsFile->getTokens();

        $returnDoc = null;
        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            if (strtolower($tokens[$tag]['content']) !== '@return') {
                continue;
            }

            if ($returnDoc !== null) {
                $error = 'Only 1 @return tag is allowed in a function comment';
                $phpcsFile->addError($error, $tag, 'DuplicateReturn');

                $this->returnDoc = $returnDoc;
                $this->returnDocIsValid = false;
                return;
            }

            if ($this->isSpecialMethod) {
                $error = sprintf('@return tag is not allowed for "%s" method', $this->methodName);
                $phpcsFile->addError($error, $tag, 'SpecialMethodReturnTag');
            }

            if ($tokens[$tag + 2]['code'] !== T_DOC_COMMENT_STRING) {
                $error = 'Return type missing for @return tag in function comment';
                $phpcsFile->addError($error, $tag, 'MissingReturnTypeDoc');

                $this->returnDoc = $tag;
                $this->returnDocIsValid = false;
                return;
            }

            $returnDoc = $tag;
        }

        if (! $returnDoc || $this->isSpecialMethod) {
            return;
        }

        $this->returnDoc = $returnDoc;

        $split = preg_split('/\s/', $tokens[$returnDoc + 2]['content'], 2);
        $this->returnDocValue = $split[0];
        $this->returnDocDescription = isset($split[1]) ? trim($split[1]) : null;

        if (strtolower($this->returnDocValue) === 'void') {
            if ($this->returnDocDescription) {
                $error = 'Description for return "void" type is not allowed.'
                    . 'Please move it to method description.';
                $phpcsFile->addError($error, $returnDoc, 'ReturnVoidDescription');

                $this->returnDocIsValid = false;
                return;
            }

            $error = 'Return tag "void" is redundant.';
            $fix = $phpcsFile->addFixableError($error, $returnDoc, 'ReturnVoid');

            if ($fix) {
                $this->removeTag($phpcsFile, $returnDoc);
            }

            $this->returnDocIsValid = false;
            return;
        }

        $isThis = in_array(strtolower($this->returnDocValue), ['$this', '$this|null', 'null|$this'], true);
        if (! $isThis
            && ! preg_match(
                '/^((?:\\\\?[a-z0-9]+)+(?:\[\])*)(\|(?:\\\\?[a-z0-9]+)+(?:\[\])*)*$/i',
                $this->returnDocValue
            )
        ) {
            $error = 'Return type has invalid format';
            $phpcsFile->addError($error, $returnDoc + 2, 'ReturnInvalidFormat');

            $this->returnDocIsValid = false;
            return;
        }

        if ($isThis
            && strtolower($this->returnDocValue) !== $this->returnDocValue
        ) {
            $error = 'Invalid case of return type; expected %s but found %s';
            $data = [
                '$this',
                $this->returnDocValue,
            ];
            $fix = $phpcsFile->addFixableError($error, $returnDoc + 2, 'InvalidReturnThis', $data);

            if ($fix) {
                $content = trim(strtolower($this->returnDocValue) . ' ' . $this->returnDocDescription);
                $phpcsFile->fixer->replaceToken($returnDoc + 2, $content);
            }
        }

        // Return tag contains only null, null[], null[][], ...
        $cleared = strtolower(strtr($this->returnDocValue, ['[' => '', ']' => '']));
        if ($cleared === 'null') {
            $error = 'Return tag contains only "null". Please specify all returned types';
            $data = [
                $this->returnDocValue,
            ];
            $code = sprintf('ReturnOnly%s', ucfirst($cleared));
            $phpcsFile->addError($error, $returnDoc + 2, $code, $data);

            $this->returnDocIsValid = false;
            return;
        }

        $hasInvalidType = false;
        $this->returnDocTypes = explode('|', $this->returnDocValue);
        $count = count($this->returnDocTypes);
        foreach ($this->returnDocTypes as $key => $type) {
            $lower = strtolower($type);

            if ($count > 1
                && ($lower === 'mixed' || strpos($lower, 'mixed[') === 0)
            ) {
                $error = 'Return type %s cannot be mixed with other types.';
                $data = [
                    $type,
                ];
                $phpcsFile->addError($error, $returnDoc + 2, 'ReturnMixed', $data);

                $hasInvalidType = true;
                continue;
            }

            if ($lower === 'void') {
                // If void is mixed up with other return types.
                $error = 'Return "void" is mixed with other types. Please use null instead.';
                $phpcsFile->addError($error, $returnDoc + 2, 'ReturnVoidWithOther');

                $hasInvalidType = true;
                continue;
            }

            if (in_array(strtolower($type), ['null', 'true', 'false'], true)) {
                $suggestedType = strtolower($type);
            } else {
                $suggestedType = $this->getSuggestedType($type);
            }
            if ($suggestedType !== $type) {
                if (strpos($suggestedType, 'self') === 0) {
                    $error = 'Return type cannot be class name. Please use "self", "static" or "$this" instead'
                        . ' depends what you expect to be returned';
                    $phpcsFile->addError($error, $returnDoc + 2, 'InvalidReturnClassName');

                    $hasInvalidType = true;
                    continue;
                }

                $error = 'Invalid return type; expected "%s", but found "%s"';
                $data = [
                    $suggestedType,
                    $type,
                ];
                $fix = $phpcsFile->addFixableError($error, $returnDoc + 2, 'InvalidReturn', $data);

                if ($fix) {
                    $this->returnDocTypes[$key] = $suggestedType;
                    $content = trim(implode('|', $this->returnDocTypes) . ' ' . $this->returnDocDescription);
                    $phpcsFile->fixer->replaceToken($returnDoc + 2, $content);
                }

                $hasInvalidType = true;
                continue;
            }
        }

        if ($hasInvalidType) {
            return;
        }

        // Check boolean values in return tag
        $lowerReturnDocTypes = explode('|', strtolower($this->returnDocValue));
        $hasTrue = in_array('true', $lowerReturnDocTypes, true);
        $hasFalse = in_array('false', $lowerReturnDocTypes, true);
        if (in_array('bool', $lowerReturnDocTypes, true)) {
            if ($hasTrue) {
                $error = 'Return tag contains "bool" and "true". Please use just "bool"';
                $fix = $phpcsFile->addFixableError($error, $returnDoc + 2, 'ReturnBoolAndTrue');

                if ($fix) {
                    $types = array_filter($this->returnDocTypes, function ($v) {
                        return strtolower($v) !== 'true';
                    });
                    $content = trim(implode('|', $types) . ' ' . $this->returnDocDescription);
                    $phpcsFile->fixer->replaceToken($returnDoc + 2, $content);
                }

                return;
            }

            if ($hasFalse) {
                $error = 'Return tag contains "bool" and "false". Please use just "bool"';
                $fix = $phpcsFile->addFixableError($error, $returnDoc + 2, 'ReturnBoolAndFalse');

                if ($fix) {
                    $types = array_filter($this->returnDocTypes, function ($v) {
                        return strtolower($v) !== 'false';
                    });
                    $content = trim(implode('|', $types) . ' ' . $this->returnDocDescription);
                    $phpcsFile->fixer->replaceToken($returnDoc + 2, $content);
                }

                return;
            }
        } elseif ($hasTrue && $hasFalse) {
            $error = 'Return tag contains "true" and "false". Please use "bool" instead.';
            $fix = $phpcsFile->addFixableError($error, $returnDoc + 2, 'ReturnTrueAndFalse');

            if ($fix) {
                $types = array_filter($this->returnDocTypes, function ($v) {
                    return ! in_array(strtolower($v), ['true', 'false'], true);
                });
                $types[] = 'bool';
                $content = trim(implode('|', $types) . ' ' . $this->returnDocDescription);
                $phpcsFile->fixer->replaceToken($returnDoc + 2, $content);
            }

            return;
        }

        // Check if types are unique.
        $uniq = array_unique($this->returnDocTypes);
        if ($uniq !== $this->returnDocTypes) {
            $expected = implode('|', $uniq);
            $error = 'Duplicated types in return tag; expected "%s", but found "%s"';
            $data = [
                $expected,
                $this->returnDocValue,
            ];
            $fix = $phpcsFile->addFixableError($error, $returnDoc + 2, 'DuplicateReturnDocTypes', $data);

            if ($fix) {
                $content = trim($expected . ' ' . $this->returnDocDescription);
                $phpcsFile->fixer->replaceToken($returnDoc + 2, $content);
            }

            return;
        }

        // Check if order of return types is as expected: first null, then simple types, and then complex.
        usort($this->returnDocTypes, function ($a, $b) {
            return $this->sortTypes($a, $b);
        });
        $content = implode('|', $this->returnDocTypes);
        if ($content !== $this->returnDocValue) {
            $error = 'Invalid order of return types in @return tag; expected "%s" but found "%s"';
            $data = [
                $content,
                $this->returnDocValue,
            ];
            $fix = $phpcsFile->addFixableError($error, $returnDoc + 2, 'ReturnTypesOrder', $data);

            if ($fix) {
                $content = trim($content . ' ' . $this->returnDocDescription);
                $phpcsFile->fixer->replaceToken($returnDoc + 2, $content);
            }
        }
    }

    /**
     * @param int $stackPtr
     */
    private function processReturnType(File $phpcsFile, $stackPtr)
    {
        // Get return type from method signature
        $returnType = $this->getReturnType($phpcsFile, $stackPtr);
        if (! $returnType) {
            return;
        }

        $this->returnType = $returnType;

        if ($this->isSpecialMethod) {
            $error = 'Method "%s" cannot declare return type';
            $data = [$this->methodName];
            $phpcsFile->addError($error, $stackPtr, 'SpecialMethodReturnType', $data);

            $this->returnTypeIsValid = false;
            return;
        }

        $colon = $phpcsFile->findPrevious(T_COLON, $returnType - 1, $stackPtr + 1);
        $firstNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, $colon + 1, null, true);

        $this->returnTypeValue = preg_replace(
            '/\s/',
            '',
            $phpcsFile->getTokensAsString($firstNonEmpty, $returnType - $firstNonEmpty + 1)
        );
        $lowerReturnTypeValue = strtolower($this->returnTypeValue);

        $suggestedType = $this->getSuggestedType($this->returnTypeValue);
        if ($suggestedType !== $this->returnTypeValue) {
            $error = 'Invalid return type; expected %s, but found %s';
            $data = [
                $suggestedType,
                $this->returnTypeValue,
            ];
            $fix = $phpcsFile->addFixableError($error, $returnType, 'InvalidReturnType', $data);

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = $firstNonEmpty; $i < $returnType; ++$i) {
                    $phpcsFile->fixer->replaceToken($i, '');
                }
                $phpcsFile->fixer->replaceToken($returnType, $suggestedType);
                $phpcsFile->fixer->endChangeset();
            }

            return;
        }

        if (! $this->returnDoc || ! $this->returnDocIsValid) {
            return;
        }

        $hasNullInDoc = preg_grep('/^null$/i', $this->returnDocTypes);

        if (! $hasNullInDoc && $this->returnTypeValue[0] === '?') {
            $error = 'Missing "null" as possible return type in PHPDocs.'
                . ' Nullable type has been found in return type declaration.';
            $fix = $phpcsFile->addFixableError($error, $this->returnDoc + 2, 'MissingNull');

            if ($fix) {
                $content = trim('null|' . $this->returnDocValue . ' ' . $this->returnDocDescription);
                $phpcsFile->fixer->replaceToken($this->returnDoc + 2, $content);
            }

            return;
        }

        if ($hasNullInDoc && $this->returnTypeValue[0] !== '?') {
            $error = 'Null type has been found in PHPDocs for return type.'
                . ' It is not declared with function return type.';
            $fix = $phpcsFile->addFixableError($error, $this->returnDoc + 2, 'AdditionalNull');

            if ($fix) {
                foreach ($this->returnDocTypes as $key => $type) {
                    if (strtolower($type) === 'null') {
                        unset($this->returnDocTypes[$key]);
                        break;
                    }
                }

                $content = trim(implode('|', $this->returnDocTypes) . ' ' . $this->returnDocDescription);
                $phpcsFile->fixer->replaceToken($this->returnDoc + 2, $content);
            }

            return;
        }

        $needSpecificationTypes = [
            'array',
            '?array',
            'iterable',
            '?iterable',
            'traversable',
            '?traversable',
            '\traversable',
            '?\traversable',
            'generator',
            '?generator',
            '\generator',
            '?\generator',
        ];

        if (! in_array($lowerReturnTypeValue, $needSpecificationTypes, true)) {
            if ($this->typesMatch($this->returnTypeValue, $this->returnDocValue)) {
                // There is no description and values are the same so PHPDoc tag is redundant.
                if (! $this->returnDocDescription) {
                    $error = 'Return tag is redundant';
                    $fix = $phpcsFile->addFixableError($error, $this->returnDoc, 'RedundantReturnDoc');

                    if ($fix) {
                        $this->removeTag($phpcsFile, $this->returnDoc);
                    }
                }

                return;
            }

            if (in_array($lowerReturnTypeValue, ['parent', '?parent'], true)) {
                if (! in_array(strtolower($this->returnDocValue), [
                    'parent',
                    'null|parent',
                    'parent|null',
                    'self',
                    'null|self',
                    'self|null',
                    'static',
                    'null|static',
                    'static|null',
                    '$this',
                    'null|$this',
                    '$this|null',
                ], true)) {
                    $error = 'Return type is "parent" so return tag must be one of:'
                        . ' "parent", "self", "static" or "$this"';
                    $phpcsFile->addError($error, $this->returnDoc + 2, 'ReturnParent');
                }

                return;
            }

            if (in_array($lowerReturnTypeValue, ['self', '?self'], true)) {
                if (! in_array(strtolower($this->returnDocValue), [
                    'self',
                    'null|self',
                    'self|null',
                    'static',
                    'null|static',
                    'static|null',
                    '$this',
                    'null|$this',
                    '$this|null',
                ], true)) {
                    $error = 'Return type is "self" so return tag must be one of: "self", "static" or "$this"';
                    $phpcsFile->addError($error, $this->returnDoc + 2, 'ReturnSelf');
                }

                return;
            }

            if (! in_array($lowerReturnTypeValue, $this->simpleReturnTypes, true)) {
                foreach ($this->returnDocTypes as $type) {
                    $lower = strtolower($type);
                    if (array_filter($this->simpleReturnTypes, function ($v) use ($lower) {
                        return $v === $lower || strpos($lower, $v . '[') === 0;
                    })) {
                        $error = 'Unexpected type "%s" found in return tag';
                        $data = [
                            $type,
                        ];
                        $phpcsFile->addError($error, $this->returnDoc + 2, 'ReturnComplexType', $data);
                    }
                }

                return;
            }

            $error = 'Return type in PHPDoc tag is different than declared type in method declaration: "%s" and "%s"';
            $data = [
                $this->returnDocValue,
                $this->returnTypeValue,
            ];
            $phpcsFile->addError($error, $this->returnDoc + 2, 'DifferentTagAndDeclaration', $data);

            return;
        }

        $simpleTypes = array_merge($this->simpleReturnTypes, ['mixed']);

        switch ($lowerReturnTypeValue) {
            case 'array':
            case '?array':
                foreach ($this->returnDocTypes as $type) {
                    if (in_array(strtolower($type), ['null', 'array'], true)) {
                        continue;
                    }

                    if (strpos($type, '[]') === false) {
                        $error = 'Return type contains "%s" which is not an array type';
                        $data = [
                            $type,
                        ];
                        $phpcsFile->addError($error, $this->returnDoc + 2, 'NotArrayType', $data);
                    }
                }
                break;

            case 'iterable':
            case '?iterable':
                foreach ($this->returnDocTypes as $type) {
                    $lower = strtolower($type);
                    if ($lower === 'iterable') {
                        continue;
                    }

                    if (in_array($lower, $simpleTypes, true)) {
                        $error = 'Return type contains "%s" which is not an iterable type';
                        $data = [
                            $type,
                        ];
                        $phpcsFile->addError($error, $this->returnDoc + 2, 'NotIterableType', $data);
                    }
                }
                break;

            case 'traversable':
            case '?traversable':
            case '\traversable':
            case '?\traversable':
                foreach ($this->returnDocTypes as $type) {
                    $lower = strtolower($type);
                    if (in_array($lower, ['null', 'traversable', '\traversable'], true)) {
                        continue;
                    }

                    if (in_array($lower, $simpleTypes, true)) {
                        $error = 'Return type contains "%s" which is not a traversable type';
                        $data = [
                            $type,
                        ];
                        $phpcsFile->addError($error, $this->returnDoc + 2, 'NotTraversableType', $data);
                    }
                }
                break;

            case 'generator':
            case '?generator':
            case '\generator':
            case '?\generator':
                foreach ($this->returnDocTypes as $type) {
                    $lower = strtolower($type);
                    if (in_array($lower, ['null', 'generator', '\generator'], true)) {
                        continue;
                    }

                    if (in_array($lower, $simpleTypes, true)) {
                        $error = 'Return type contains "%s" which is not a generator type';
                        $data = [
                            $type,
                        ];
                        $phpcsFile->addError($error, $this->returnDoc + 2, 'NotGeneratorType', $data);
                    }
                }
                break;
        }
    }

    /**
     * @param int $stackPtr
     */
    private function processReturnStatements(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Method does not have a body.
        if (! isset($tokens[$stackPtr]['scope_opener'])) {
            return;
        }

        $returnValues = [];

        // Search all return/yield/yield from in the method.
        for ($i = $tokens[$stackPtr]['scope_opener'] + 1; $i < $tokens[$stackPtr]['scope_closer']; ++$i) {
            // Skip closures and anonymous classes.
            if ($tokens[$i]['code'] === T_CLOSURE
                || $tokens[$i]['code'] === T_ANON_CLASS
            ) {
                $i = $tokens[$i]['scope_closer'];
                continue;
            }

            if ($tokens[$i]['code'] !== T_RETURN
                && $tokens[$i]['code'] !== T_YIELD
                && $tokens[$i]['code'] !== T_YIELD_FROM
            ) {
                continue;
            }

            $next = $phpcsFile->findNext(Tokens::$emptyTokens, $i + 1, null, true);
            if ($tokens[$next]['code'] === T_SEMICOLON) {
                $this->returnCodeVoid($phpcsFile, $i);
            } else {
                $this->returnCodeValue($phpcsFile, $i);
                $returnValues[$next] = $this->getReturnValue($phpcsFile, $next);

                if ($this->returnDoc
                    && $this->returnDocIsValid
                    && in_array(strtolower($this->returnDocValue), ['$this', 'null|$this', '$this|null'], true)
                ) {
                    $isThis = ! in_array($tokens[$next]['code'], [
                        T_CLOSURE,
                        T_CONSTANT_ENCAPSED_STRING,
                        T_DIR,
                        T_DOUBLE_QUOTED_STRING,
                        T_FILE,
                        T_NEW,
                        T_NS_SEPARATOR,
                        T_OPEN_PARENTHESIS,
                        T_OBJECT_CAST,
                        T_SELF,
                        T_STATIC,
                        T_STRING_CAST,
                        T_PARENT,
                    ], true);

                    if ($isThis
                        && $tokens[$next]['code'] === T_VARIABLE
                        && (strtolower($tokens[$next]['content']) !== '$this'
                            || (($next = $phpcsFile->findNext(Tokens::$emptyTokens, $next + 1, null, true))
                                && $tokens[$next]['code'] !== T_SEMICOLON))
                    ) {
                        $isThis = false;
                    }

                    if (! $isThis) {
                        $error = 'Return type of "%s" function is "$this",'
                            . ' but function is returning not $this here';
                        $data = [$this->methodName];
                        $phpcsFile->addError($error, $i, 'InvalidReturnNotThis', $data);
                    }
                }
            }
        }

        if (! $returnValues
            && (($this->returnDoc && $this->returnDocIsValid)
                || ($this->returnType && $this->returnTypeIsValid && strtolower($this->returnTypeValue) !== 'void'))
        ) {
            $error = 'Return type of "%s" function is not void, but function has no return statement';
            $data = [$this->methodName];
            $phpcsFile->addError(
                $error,
                $this->returnDoc && $this->returnDocIsValid ? $this->returnDoc : $this->returnType,
                'InvalidNoReturn',
                $data
            );
        }

        if (! $returnValues || ! $this->returnDoc || ! $this->returnDocIsValid) {
            return;
        }

        $uniq = array_unique($returnValues);
        if (count($uniq) === 1) {
            // We have to use current because index in the array is $ptr
            switch (current($uniq)) {
                case 'array':
                    if ($matches = array_udiff(
                        preg_grep('/[^\]]$/', $this->returnDocTypes),
                        ['null', 'array', 'iterable'],
                        function ($a, $b) {
                            return strcasecmp($a, $b);
                        }
                    )) {
                        $error = 'Function returns only array, but return type contains not array types: %s';
                        $data = [
                            implode(', ', $matches),
                        ];
                        $phpcsFile->addError($error, $this->returnDoc, 'ReturnArrayOnly', $data);
                    }
                    break;

                case 'bool':
                    if (! in_array(strtolower($this->returnDocValue), ['bool', 'boolean'], true)) {
                        $error = 'Functions returns only boolean value, but return type is not only bool';
                        $phpcsFile->addError($error, $this->returnDoc, 'ReturnBoolOnly');
                    }
                    break;

                case 'false':
                    if (strtolower($this->returnDocValue) !== 'false') {
                        $error = 'Function returns only boolean false, but return type is not only false';
                        $phpcsFile->addError($error, $this->returnDoc, 'ReturnFalseOnly');
                    }
                    break;

                case 'true':
                    if (strtolower($this->returnDocValue) !== 'true') {
                        $error = 'Function returns only boolean true, but return type is not only true';
                        $phpcsFile->addError($error, $this->returnDoc, 'ReturnTrueOnly');
                    }
                    break;

                case 'new':
                    $instances = [];
                    foreach ($returnValues as $ptr => $new) {
                        $next = $phpcsFile->findNext(Tokens::$emptyTokens, $ptr + 1, null, true);
                        if ($tokens[$next]['code'] !== T_STRING
                            && $tokens[$next]['code'] !== T_NS_SEPARATOR
                        ) {
                            // It is unknown instance, break switch.
                            break 2;
                        }

                        $after = $phpcsFile->findNext(
                            Tokens::$emptyTokens
                                + [T_NS_SEPARATOR => T_NS_SEPARATOR, T_STRING => T_STRING],
                            $next + 1,
                            null,
                            true
                        );

                        $last = $phpcsFile->findPrevious(T_STRING, $after - 1, $next);
                        $content = $this->getSuggestedType(
                            $phpcsFile->getTokensAsString($next, $last - $next + 1)
                        );

                        $instances[strtolower($content)] = $content;
                    }

                    // If function returns instances of different types, break.
                    if (count($instances) !== 1) {
                        break;
                    }

                    $className = current($instances);
                    if ($this->returnDocValue !== $className) {
                        $error = 'Function returns only new instance of %s, but return type is not only %s';
                        $data = [
                            $className,
                            $className,
                        ];
                        $phpcsFile->addError($error, $this->returnDoc + 2, 'ReturnNewInstanceOnly', $data);
                    }
                    break;

                case '$this':
                    if (($isClassName = $this->isClassName($this->returnDocValue))
                        || strtolower($this->returnDocValue) === 'self'
                    ) {
                        $error = 'Function returns only $this so return type should be $this instead of '
                            . ($isClassName ? 'class name' : 'self');
                        $fix = $phpcsFile->addFixableError($error, $this->returnDoc + 2, 'ReturnThisOnly');

                        if ($fix) {
                            $content = trim('$this ' . $this->returnDocDescription);
                            $phpcsFile->fixer->replaceToken($this->returnDoc + 2, $content);
                        }
                    }
                    break;
            }
        }
    }

    /**
     * @param int $ptr
     * @return string
     */
    private function getReturnValue(File $phpcsFile, $ptr)
    {
        $tokens = $phpcsFile->getTokens();

        switch ($tokens[$ptr]['code']) {
            case T_ARRAY:
            case T_ARRAY_CAST:
            case T_OPEN_SHORT_ARRAY:
                if (! $this->hasCorrectType(['array', '?array', 'iterable', '?iterable'], [])
                    || ($this->returnDoc
                        && $this->returnDocIsValid
                        && strpos($this->returnDocValue, '[]') === false
                        && ! array_intersect(
                            explode('|', strtolower($this->returnDocValue)),
                            ['array', 'iterable']
                        ))
                ) {
                    $error = 'Function return type is array nor iterable, but function returns array here';
                    $phpcsFile->addError($error, $ptr, 'ReturnArray');
                }
                return 'array';

            case T_BOOL_CAST:
            case T_BOOLEAN_NOT:
                $end = $ptr;
                while (++$end) {
                    if ($tokens[$end]['code'] === T_OPEN_PARENTHESIS) {
                        $end = $tokens[$end]['parenthesis_closer'];
                        continue;
                    }
                    if ($tokens[$end]['code'] === T_OPEN_SQUARE_BRACKET
                        || $tokens[$end]['code'] === T_OPEN_CURLY_BRACKET
                        || $tokens[$end]['code'] === T_OPEN_SHORT_ARRAY
                    ) {
                        $end = $tokens[$end]['bracket_closer'];
                        continue;
                    }

                    if (in_array($tokens[$end]['code'], [T_SEMICOLON, T_INLINE_THEN], true)) {
                        break;
                    }
                }
                if ($tokens[$end]['code'] !== T_SEMICOLON) {
                    return 'unknown';
                }

                if (! $this->hasCorrectType(['bool', '?bool'], ['bool', 'boolean'])) {
                    $error = 'Function return type is not bool, but function returns boolean value here';
                    $phpcsFile->addError($error, $ptr, 'ReturnFloat');
                }
                return 'bool';

            case T_FALSE:
                if (! $this->hasCorrectType(['bool', '?bool'], ['bool', 'boolean', 'false'])) {
                    $error = 'Function return type is not bool, but function returns boolean false here';
                    $phpcsFile->addError($error, $ptr, 'ReturnFalse');
                }
                return 'false';

            case T_TRUE:
                if (! $this->hasCorrectType(['bool', '?bool'], ['bool', 'boolean', 'true'])) {
                    $error = 'Function return type is not bool, but function returns boolean true here';
                    $phpcsFile->addError($error, $ptr, 'ReturnTrue');
                }
                return 'true';

            // integer value or integer cast
            case T_LNUMBER:
                $next = $phpcsFile->findNext(Tokens::$emptyTokens, $ptr + 1, null, true);
                if ($tokens[$next]['code'] !== T_SEMICOLON) {
                    return 'unknown';
                }
                // no break
            case T_INT_CAST:
                if (! $this->hasCorrectType(['int', '?int'], ['int', 'integer'])) {
                    $error = 'Function return type is not int, but function return int here';
                    $phpcsFile->addError($error, $ptr, 'ReturnInt');
                }
                return 'int';

            // float value or float cast
            case T_DNUMBER:
            case T_DOUBLE_CAST:
                if (! $this->hasCorrectType(['float', '?float'], ['double', 'float', 'real'])) {
                    $error = 'Function return type is not float, but function returns float here';
                    $phpcsFile->addError($error, $ptr, 'ReturnFloat');
                }
                return 'float';

            case T_NEW:
                return 'new';

            case T_NULL:
                if (! $this->hasCorrectType([], ['null'])
                    || ($this->returnType
                        && $this->returnTypeIsValid
                        && strpos($this->returnTypeValue, '?') !== 0)
                ) {
                    $error = 'Function return type is not nullable, but function returns null here';
                    $phpcsFile->addError($error, $ptr, 'ReturnNull');
                }
                return 'null';

            case T_VARIABLE:
                if (strtolower($tokens[$ptr]['content']) !== '$this') {
                    return 'variable';
                }

                $next = $phpcsFile->findNext(Tokens::$emptyTokens, $ptr + 1, null, true);
                if ($tokens[$next]['code'] !== T_SEMICOLON) {
                    // This is not "$this" return but something else.
                    return 'unknown';
                }
                return '$this';
        }

        return 'unknown';
    }

    /**
     * @param string[] $expectedType
     * @param string[] $expectedDoc
     * @return bool
     */
    private function hasCorrectType(array $expectedType, array $expectedDoc)
    {
        if ($expectedType
            && $this->returnType
            && $this->returnTypeIsValid
            && ! in_array(strtolower($this->returnTypeValue), $expectedType, true)
        ) {
            return false;
        }

        if ($expectedDoc
            && $this->returnDoc
            && $this->returnDocIsValid
            && ! array_filter($this->returnDocTypes, function ($v) use ($expectedDoc) {
                return in_array(strtolower($v), $expectedDoc, true);
            })
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param int $ptr
     */
    private function returnCodeVoid(File $phpcsFile, $ptr)
    {
        if (($this->returnDoc && $this->returnDocIsValid)
            || ($this->returnType && $this->returnTypeIsValid && strtolower($this->returnTypeValue) !== 'void')
        ) {
            $error = 'Return type of "%s" function is not void, but function is returning void here';
            $data = [$this->methodName];
            $phpcsFile->addError($error, $ptr, 'InvalidReturnNotVoid', $data);
        }
    }

    /**
     * @param int $ptr
     */
    private function returnCodeValue(File $phpcsFile, $ptr)
    {
        // Special method cannot return any values.
        if ($this->isSpecialMethod) {
            $error = 'Method "%s" cannot return any value, but returns it here';
            $data = [$this->methodName];
            $phpcsFile->addError($error, $ptr, 'SpecialMethodReturnValue', $data);

            return;
        }

        // Function is void but return a value.
        if ((! $this->returnType
                || ! $this->returnTypeIsValid
                || $this->returnTypeValue === 'void')
            && (! $this->returnDoc
                || ! $this->returnDocIsValid
                || $this->returnDocValue === 'void')
        ) {
            $error = 'Function "%s" returns value but it is not specified.'
                . ' Please add return tag or declare return type.';
            $data = [
                $this->methodName,
            ];
            $phpcsFile->addError($error, $ptr, 'ReturnValue', $data);
        }
    }
}

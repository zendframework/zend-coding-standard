<?php

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use ZendCodingStandard\Helper\Methods;

use function array_filter;
use function array_merge;
use function array_unique;
use function count;
use function current;
use function explode;
use function implode;
use function in_array;
use function key;
use function preg_grep;
use function preg_match;
use function preg_split;
use function sprintf;
use function str_replace;
use function stripos;
use function strpos;
use function strtolower;
use function strtr;
use function trim;
use function ucfirst;
use function usort;

use const T_DOC_COMMENT_STAR;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_WHITESPACE;
use const T_FUNCTION;
use const T_NS_SEPARATOR;
use const T_NULLABLE;
use const T_STRING;

class ParamSniff implements Sniff
{
    use Methods;

    /**
     * Method parameters.
     *
     * @var array
     */
    private $params = [];

    /**
     * Processed parameters.
     *
     * @var array
     */
    private $processedParams = [];

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

        $this->processedParams = [];
        $this->params = $phpcsFile->getMethodParameters($stackPtr);

        if ($commentStart = $this->getCommentStart($phpcsFile, $stackPtr)) {
            $this->processParamDoc($phpcsFile, $commentStart);
        }
        $this->processParamSpec($phpcsFile);
    }

    /**
     * @param int $commentStart
     */
    private function processParamDoc(File $phpcsFile, $commentStart)
    {
        $params = [];
        $paramsMap = [];
        $tokens = $phpcsFile->getTokens();

        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            if (strtolower($tokens[$tag]['content']) !== '@param') {
                continue;
            }

            if ($tokens[$tag + 2]['code'] !== T_DOC_COMMENT_STRING) {
                $error = 'Param type ane name missing for @param tag in function comment';
                $phpcsFile->addError($error, $tag, 'MissingParamDetailsDoc');

                continue;
            }

            $split = preg_split('/\s/', $tokens[$tag + 2]['content'], 3);

            if (! isset($split[1])) {
                if ($this->isVariable($split[0])) {
                    $error = 'Missing param type for param %s';
                    $data = [
                        $split[0],
                    ];
                    $phpcsFile->addError($error, $tag + 2, 'MissingParamTypeDoc', $data);
                } else {
                    $error = 'Missing parameter name in PHPDOcs';
                    $phpcsFile->addError($error, $tag + 2, 'MissingParamNameDoc');
                }

                continue;
            }

            if (! $this->isVariable($split[1])) {
                $error = 'Invalid parameter name';
                $phpcsFile->addError($error, $tag + 2, 'InvalidParamNameDoc');
                continue;
            }
            $name = $split[1];

            $clearName = strtolower(str_replace('.', '', $name));
            if (in_array($clearName, $params, true)) {
                $error = 'Param tag is duplicated for parameter %s';
                $data = [
                    $name,
                ];
                $phpcsFile->addError($error, $tag + 2, 'DuplicatedParamTag', $data);
                continue;
            }
            $params[] = $clearName;

            $param = array_filter($this->params, function (array $param) use ($clearName) {
                return strtolower($param['name']) === $clearName;
            });

            if (! $param) {
                $error = 'Parameter %s has not been found in function declaration';
                $data = [
                    $name,
                ];
                $phpcsFile->addError($error, $tag + 2, 'NoParameter', $data);
                continue;
            }

            // Add param to processed list, even if it may not be checked.
            $this->processedParams[] = key($param);
            $paramsMap[key($param)] = ['token' => $tag, 'name' => $name];

            if (! $this->isType($split[0])) {
                $error = 'Invalid type for param %s';
                $data = [
                    $split[1],
                ];
                $phpcsFile->addError($error, $tag + 2, 'InvalidParamTypeDoc', $data);
                continue;
            }
            $description = isset($split[2]) ? $split[2] : null;
            $type = $split[0];

            $this->checkParam($phpcsFile, current($param), $tag, $name, $type, $description);
        }

        $last = current($this->processedParams);
        foreach ($this->processedParams as $current) {
            if ($last > $current) {
                $error = 'Wrong param order, the first wrong is %s';
                $data = [
                    $paramsMap[$current]['name'],
                ];
                $fix = $phpcsFile->addFixableError($error, $paramsMap[$current]['token'], 'WrongParamOrder', $data);

                if ($fix) {
                    $this->fixParamOrder($phpcsFile, $paramsMap, $current);
                }

                break;
            }

            $last = $current;
        }
    }

    /**
     * @param string[] $map
     * @param int $wrong
     */
    private function fixParamOrder(File $phpcsFile, array $map, $wrong)
    {
        $tokens = $phpcsFile->getTokens();

        $tagPtr = $map[$wrong]['token'];

        $line = $tokens[$tagPtr]['line'];
        // Find first element in line with token, all it will be moved.
        $start = $phpcsFile->findFirstOnLine([], $tagPtr, true);

        $end = $tagPtr;
        while (true) {
            while ($tokens[$end + 1]['line'] === $line) {
                ++$end;
            }

            $next = $phpcsFile->findNext(
                [T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STAR],
                $end,
                null,
                true
            );

            if ($tokens[$next]['code'] !== T_DOC_COMMENT_STRING
                || $tokens[$next]['line'] !== $line + 1
            ) {
                break;
            }

            ++$line;
            $end = $next;
        }

        $contentToMove = $phpcsFile->getTokensAsString($start, $end - $start + 1);

        // Where to move?
        foreach ($map as $key => $data) {
            if ($key > $wrong) {
                $moveBefore = $phpcsFile->findFirstOnLine([], $data['token'], true);
                break;
            }
        }

        $phpcsFile->fixer->beginChangeset();
        // Remove param from the old position.
        for ($i = $start; $i <= $end; ++$i) {
            $phpcsFile->fixer->replaceToken($i, '');
        }
        // Put param in the new position.
        $phpcsFile->fixer->addContentBefore($moveBefore, $contentToMove);
        $phpcsFile->fixer->endChangeset();
    }

    /**
     * @param int $varPtr
     * @param string $newTypeHint
     */
    private function replaceParamTypeHint(File $phpcsFile, $varPtr, $newTypeHint)
    {
        $last = $phpcsFile->findPrevious(T_STRING, $varPtr - 1);
        $first = $phpcsFile->findPrevious([T_NULLABLE, T_STRING, T_NS_SEPARATOR], $last, null, true);

        $phpcsFile->fixer->beginChangeset();
        $phpcsFile->fixer->replaceToken($last, $newTypeHint);
        for ($i = $last - 1; $i > $first; --$i) {
            $phpcsFile->fixer->replaceToken($i, '');
        }
        $phpcsFile->fixer->endChangeset();
    }

    /**
     * @param string[] $param Real param function details.
     * @param null|int $tagPtr Position of the @param tag.
     * @param null|string $name Name of the param in the @param tag.
     * @param null|string $typeStr Type of the param in the @param tag.
     * @param null|string $description Description of the param in the @param tag.
     */
    private function checkParam(
        File $phpcsFile,
        array $param,
        $tagPtr = null,
        $name = null,
        $typeStr = null,
        $description = null
    ) {
        $typeHint = $param['type_hint'];

        if ($typeHint) {
            $suggestedType = $this->getSuggestedType($typeHint);

            if ($suggestedType !== $typeHint) {
                $error = 'Invalid type hint for param %s; expected "%s", but found "%s"';
                $data = [
                    $param['name'],
                    $suggestedType,
                    $typeHint,
                ];
                $fix = $phpcsFile->addFixableError($error, $param['token'], 'InvalidTypeHint', $data);

                if ($fix) {
                    $this->replaceParamTypeHint(
                        $phpcsFile,
                        $param['token'],
                        $suggestedType
                    );
                }

                $typeHint = $suggestedType;
            }
        }
        $lowerTypeHint = strtolower($typeHint);

        // There is no param tag for the parameter
        if (! $tagPtr) {
            if (! $typeHint) {
                $error = 'Parameter %s needs specification in PHPDocs';
                $data = [
                    $param['name'],
                ];
                $phpcsFile->addError($error, $param['token'], 'MissingSpecification', $data);
            }

            return;
        }

        $clearName = str_replace('.', '', $name);
        $isVariadic = $name !== $clearName;

        if ($param['name'] !== $clearName) {
            $error = 'Parameter name is not consistent, found: "%s" and "%s"';
            $data = [
                $clearName,
                $param['name'],
            ];
            $phpcsFile->addError($error, $tagPtr, 'InconsistentParamName', $data);
        }

        $isSpecVariadic = $param['variable_length'] === true;
        if ($isVariadic xor $isSpecVariadic) {
            $error = 'Parameter variadic inconsistent';
            $phpcsFile->addError($error, $tagPtr, 'InconsistentVariadic');
        }

        $types = explode('|', $typeStr);

        // Check if types are unique.
        $uniq = array_unique($types);
        if ($uniq !== $types) {
            $expected = implode('|', $uniq);
            $error = 'Duplicated types in param tag; expected "%s", but found "%s"';
            $data = [
                $expected,
                implode('|', $types),
            ];
            $fix = $phpcsFile->addFixableError($error, $tagPtr + 2, 'DuplicateParamDocTypes', $data);

            if ($fix) {
                $content = trim($expected . ' ' . $name . ' ' . $description);
                $phpcsFile->fixer->replaceToken($tagPtr + 2, $content);
            }

            return;
        }

        // Check if null is one of the types
        if (($param['nullable_type']
                || (isset($param['default']) && strtolower($param['default']) === 'null'))
            && ! preg_grep('/^null$/i', $types)
        ) {
            $error = 'Missing type "null" for nullable parameter %s';
            $data = [
                $param['name'],
            ];
            $fix = $phpcsFile->addFixableError($error, $tagPtr + 2, 'ParamDocMissingNull', $data);

            if ($fix) {
                $content = trim('null|' . implode('|', $types) . ' ' . $name . ' ' . $description);
                $phpcsFile->fixer->replaceToken($tagPtr + 2, $content);
            }
        }

        $count = count($types);
        $break = false;
        foreach ($types as $key => $type) {
            $lower = strtolower($type);

            if ($lower === 'null'
                && $typeHint
                && ! $param['nullable_type']
                && (! isset($param['default'])
                    || $param['default'] !== 'null')
            ) {
                $error = 'Param %s cannot have "null" value';
                $data = [
                    $name,
                ];
                $fix = $phpcsFile->addFixableError($error, $tagPtr + 2, 'ParamDocNull', $data);

                if ($fix) {
                    unset($types[$key]);
                    $content = trim(implode('|', $types) . ' ' . $name . ' ' . $description);
                    $phpcsFile->fixer->replaceToken($tagPtr + 2, $content);
                }

                $break = true;
                continue;
            }

            if (stripos($type, 'null[') === 0) {
                $error = 'Param type "%s" is not a valid type';
                $data = [
                    $type,
                ];
                $phpcsFile->addError($error, $tagPtr + 2, 'ParamDocNullArray', $data);

                $break = true;
                continue;
            }

            if ($count > 1
                && ($lower === 'mixed' || strpos($lower, 'mixed[') === 0)
            ) {
                $error = 'Param type "%s" cannot be mixed with other types.';
                $data = [
                    $type,
                ];
                $phpcsFile->addError($error, $tagPtr + 2, 'ParamDocMixed', $data);

                $break = true;
                continue;
            }

            $clearType = strtr($lower, ['[' => '', ']' => '']);
            if (in_array($clearType, ['void', 'true', 'false'], true)) {
                $error = 'Invalid param type: "%s"';
                $code = sprintf('InvalidParam%sType', ucfirst($clearType));
                $data = [
                    $type,
                ];
                $phpcsFile->addError($error, $tagPtr + 2, $code, $data);

                $break = true;
                continue;
            }

            $suggestedType = $this->getSuggestedType($type);
            if ($suggestedType !== $type) {
                $error = 'Invalid param type; expected "%s", but found "%s"';
                $data = [
                    $suggestedType,
                    $type,
                ];
                $fix = $phpcsFile->addFixableError($error, $tagPtr + 2, 'InvalidParamDocType', $data);

                if ($fix) {
                    $types[$key] = $suggestedType;
                    $content = trim(implode('|', $types) . ' ' . $name . ' ' . $description);
                    $phpcsFile->fixer->replaceToken($tagPtr + 2, $content);
                }

                $break = true;
                continue;
            }

            if ($typeHint) {
                $simpleTypes = array_merge($this->simpleReturnTypes, ['mixed']);

                // array
                if (in_array($lowerTypeHint, ['array', '?array'], true)
                    && ! in_array($lower, ['null', 'array'], true)
                    && strpos($type, '[]') === false
                ) {
                    $error = 'Param type contains "%s" which is not an array type';
                    $data = [
                        $type,
                    ];
                    $phpcsFile->addError($error, $tagPtr + 2, 'NotArrayType', $data);

                    $break = true;
                    continue;
                }

                // iterable
                if (in_array($lowerTypeHint, ['iterable', '?iterable'], true)
                    && in_array($lower, $simpleTypes, true)
                ) {
                    $error = 'Param type contains "%s" which is not an iterable type';
                    $data = [
                        $type,
                    ];
                    $phpcsFile->addError($error, $tagPtr + 2, 'NotIterableType', $data);

                    $break = true;
                    continue;
                }

                // traversable
                if (in_array($lowerTypeHint, [
                        'traversable',
                        '?traversable',
                        '\traversable',
                        '?\traversable',
                    ], true)
                    && ! in_array($lower, ['null', 'traversable', '\traversable'], true)
                    && in_array($lower, $simpleTypes, true)
                ) {
                    $error = 'Param type contains "%s" which is not a traversable type';
                    $data = [
                        $type,
                    ];
                    $phpcsFile->addError($error, $tagPtr + 2, 'NotTraversableType', $data);

                    $break = true;
                    continue;
                }

                // generator
                if (in_array($lowerTypeHint, [
                        'generator',
                        '?generator',
                        '\generator',
                        '?\generator',
                    ], true)
                    && ! in_array($lower, ['null', 'generator', '\generator'], true)
                    && in_array($lower, array_merge($simpleTypes, ['mixed']), true)
                ) {
                    $error = 'Param type contains %s which is not a generator type';
                    $data = [
                        $type,
                    ];
                    $phpcsFile->addError($error, $tagPtr + 2, 'NotGeneratorType', $data);

                    $break = true;
                    continue;
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

                if (! in_array($lowerTypeHint, $needSpecificationTypes, true)
                    && ((in_array($lowerTypeHint, $simpleTypes, true)
                            && $lower !== 'null'
                            && $lower !== $lowerTypeHint
                            && '?' . $lower !== $lowerTypeHint)
                        || (! in_array($lowerTypeHint, $simpleTypes, true)
                            && array_filter($simpleTypes, function ($v) use ($lower) {
                                return $v === $lower || strpos($lower, $v . '[') === 0;
                            })))
                ) {
                    $error = 'Invalid type "%s" for parameter %s';
                    $data = [
                        $type,
                        $name,
                    ];
                    $phpcsFile->addError($error, $tagPtr, 'ParamDocInvalidType', $data);

                    $break = true;
                    continue;
                }
            }
        }

        // If some parameter is invalid, we don't want to preform other checks
        if ($break) {
            return;
        }

        // Check if order of return types is as expected: first null, then simple types, and then complex.
        $unsorted = implode('|', $types);
        usort($types, function ($a, $b) {
            return $this->sortTypes($a, $b);
        });
        $content = implode('|', $types);
        if ($content !== $unsorted) {
            $error = 'Invalid order of param types in @param tag; expected "%s" but found "%s"';
            $data = [
                $content,
                $unsorted,
            ];
            $fix = $phpcsFile->addFixableError($error, $tagPtr + 2, 'ReturnTypesOrder', $data);

            if ($fix) {
                $content = trim($content . ' ' . $name . ' ' . $description);
                $phpcsFile->fixer->replaceToken($tagPtr + 2, $content);
            }
        }

        // Check if PHPDocs param is required
        if ($typeHint && ! $description) {
            $tmpTypeHint = $typeHint;
            if (isset($param['default'])
                && strtolower($param['default']) === 'null'
                && $tmpTypeHint[0] !== '?'
            ) {
                $tmpTypeHint = '?' . $tmpTypeHint;
            }

            if ($this->typesMatch($tmpTypeHint, $typeStr)) {
                $error = 'Param tag is redundant';
                $fix = $phpcsFile->addFixableError($error, $tagPtr, 'RedundantParamDoc');

                if ($fix) {
                    $this->removeTag($phpcsFile, $tagPtr);
                }
            }
        }
    }

    /**
     * @param string $str
     * @return bool
     */
    private function isVariable($str)
    {
        return strpos($str, '$') === 0
            || strpos($str, '...$') === 0;
    }

    /**
     * @param string $str
     * @return bool
     */
    private function isType($str)
    {
        return (bool) preg_match('/^((?:\\\\?[a-z0-9]+)+(?:\[\])*)(\|(?:\\\\?[a-z0-9]+)+(?:\[\])*)*$/i', $str);
    }

    private function processParamSpec(File $phpcsFile)
    {
        foreach ($this->params as $k => $param) {
            if (in_array($k, $this->processedParams, true)) {
                continue;
            }

            $this->checkParam($phpcsFile, $param);
        }
    }
}

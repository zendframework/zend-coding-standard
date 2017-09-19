<?php
namespace ZendCodingStandard\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use ZendCodingStandard\Helper\Methods;

use function array_filter;
use function array_unique;
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

use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_STAR;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_WHITESPACE;
use const T_FUNCTION;
use const T_NS_SEPARATOR;
use const T_NULLABLE;
use const T_STRING;
use const T_WHITESPACE;

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
            $this->processParamDoc($phpcsFile, $stackPtr, $commentStart);
        }
        $this->processParamSpec($phpcsFile, $stackPtr);
    }

    /**
     * @param int $stackPtr
     * @param int $commentStart
     */
    private function processParamDoc(File $phpcsFile, $stackPtr, $commentStart)
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

            $this->checkParam($phpcsFile, current($param), $stackPtr, $tag, $name, $type, $description);
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
     * @param null|int $methodPtr Position of the method definition token.
     * @param null|int $tagPtr Position of the @param tag.
     * @param null|string $name Name of the param in the @param tag.
     * @param null|string $typeStr Type of the param in the @param tag.
     * @param null|string $description Description of the param in the @param tag.
     */
    private function checkParam(
        File $phpcsFile,
        array $param,
        $methodPtr = null,
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
            } elseif (in_array($lowerTypeHint, $this->needSpecificationTypes, true)) {
                $type = strtr($lowerTypeHint, ['\\' => '', '?' => '']);
                $code = sprintf('ParamType%sSpecification', ucfirst($type));
                $error = 'Parameter "%s" needs better specification in PHPDocs';
                $data = [
                    $param['name'],
                ];
                $phpcsFile->addError($error, $param['token'], $code, $data);
            } elseif (isset($param['default'])
                && strtolower($param['default']) === 'null'
                && $typeHint[0] !== '?'
            ) {
                $error = 'Parameter %s needs specification in PHPDocs';
                $data = [
                    $param['name'],
                ];
                $fix = $phpcsFile->addFixableError($error, $param['token'], 'MissingSpecificationNUll', $data);

                if ($fix) {
                    $this->addParameter($phpcsFile, $methodPtr, $param['name'], 'null|' . $param['type_hint']);
                }
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

            if ($lower === 'mixed'
                || stripos($type, 'mixed[') === 0
            ) {
                $error = 'Param type "mixed" is not allowed. Please specify the type.';
                $phpcsFile->addError($error, $tagPtr + 2, 'ParamDocMixed');

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

            if (array_filter($this->needSpecificationTypes, function ($v) use ($lower) {
                return $lower === $v || strpos($lower, $v . '[') === 0;
            })) {
                $type = str_replace('\\', '', $lower);
                $code = sprintf('Param%sSpecification', ucfirst($type));
                $data = [
                    stripos($type, 'traversable') !== false ? ucfirst($type) : $type,
                ];
                $error = 'Param type "%s" needs better specification';
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
                    && in_array($lower, $this->simpleReturnTypes, true)
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
                    && (strpos($type, '[]') !== false
                        || in_array($lower, $this->simpleReturnTypes, true))
                ) {
                    $error = 'Param type contains "%s" which is not a traversable type';
                    $data = [
                        $type,
                    ];
                    $phpcsFile->addError($error, $tagPtr + 2, 'NotTraversableType', $data);

                    $break = true;
                    continue;
                }

                if (! in_array($lowerTypeHint, $this->needSpecificationTypes, true)
                    && ((in_array($lowerTypeHint, $this->simpleReturnTypes, true)
                            && $lower !== 'null'
                            && $lower !== $lowerTypeHint
                            && '?' . $lower !== $lowerTypeHint)
                        || (! in_array($lowerTypeHint, $this->simpleReturnTypes, true)
                            && array_filter($this->simpleReturnTypes, function ($v) use ($lower) {
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
        if ($typeHint
            && ! in_array($lowerTypeHint, $this->needSpecificationTypes, true)
            && $this->typesMatch($typeHint, $typeStr)
            && ! $description
        ) {
            $error = 'Param tag is redundant';
            $fix = $phpcsFile->addFixableError($error, $tagPtr, 'RedundantParamDoc');

            if ($fix) {
                $this->removeTag($phpcsFile, $tagPtr);
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

    /**
     * @param int $stackPtr
     */
    private function processParamSpec(File $phpcsFile, $stackPtr)
    {
        foreach ($this->params as $k => $param) {
            if (in_array($k, $this->processedParams, true)) {
                continue;
            }

            $this->checkParam($phpcsFile, $param, $stackPtr);
        }
    }

    /**
     * @param int $methodPtr
     * @param string $name
     * @param string $type
     */
    private function addParameter(File $phpcsFile, $methodPtr, $name, $type)
    {
        $tokens = $phpcsFile->getTokens();

        $skip = Tokens::$methodPrefixes
            + [T_WHITESPACE => T_WHITESPACE];

        $commentEnd = $phpcsFile->findPrevious($skip, $methodPtr - 1, null, true);
        if ($tokens[$commentEnd]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
            $firstOnLine = $phpcsFile->findFirstOnLine([], $commentEnd, true);
            $indent = ' ';
            $content = '* @param %3$s %4$s%2$s%1$s';
            if ($tokens[$firstOnLine]['code'] === T_DOC_COMMENT_WHITESPACE) {
                $indent = $tokens[$firstOnLine]['content'];
            } elseif ($tokens[$firstOnLine]['code'] === T_WHITESPACE) {
                $indent .= $tokens[$firstOnLine]['content'];
                $content = '%2$s%1$s* @param %3$s %4$s%2$s%1$s';
            }

            $before = $commentEnd;
        } else {
            $next = $phpcsFile->findNext(T_WHITESPACE, $commentEnd + 1, null, true);
            $firstOnLine = $phpcsFile->findFirstOnLine([], $next, true);
            $indent = '';
            if ($tokens[$firstOnLine]['code'] === T_WHITESPACE) {
                $indent = $tokens[$firstOnLine]['content'];
            }

            $content = '%1$s/**%2$s'
                . '%1$s * @param %3$s %4$s%2$s'
                . '%1$s */%2$s';

            $before = $firstOnLine;
        }

        $content = sprintf(
            $content,
            $indent,
            $phpcsFile->eolChar,
            $type,
            $name
        );

        $phpcsFile->fixer->addContentBefore($before, $content);
    }
}

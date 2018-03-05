<?php

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use ZendCodingStandard\Helper\Methods;

use function array_filter;
use function array_shift;
use function array_unique;
use function count;
use function end;
use function explode;
use function implode;
use function in_array;
use function preg_split;
use function sprintf;
use function strpos;
use function strtolower;
use function strtr;
use function substr;
use function trim;
use function ucfirst;
use function usort;

use const T_DOC_COMMENT_OPEN_TAG;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_TAG;
use const T_DOC_COMMENT_WHITESPACE;

class TagWithTypeSniff implements Sniff
{
    use Methods;

    /**
     * @var string[]
     */
    public $tags = [
        '@param',
        '@return',
        '@var',
    ];

    /**
     * @var null|string
     */
    private $type;

    /**
     * @var array
     */
    private $types = [];

    /**
     * @var null|string
     */
    private $description;

    /**
     * @return int[]
     */
    public function register()
    {
        return [T_DOC_COMMENT_TAG];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $this->initScope($phpcsFile, $stackPtr);

        $this->type = null;
        $this->types = [];
        $this->description = null;

        $tokens = $phpcsFile->getTokens();

        $tag = strtolower($tokens[$stackPtr]['content']);
        if (! in_array($tag, $this->tags, true)) {
            return;
        }

        $string = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $stackPtr + 1);
        if ($string !== $stackPtr + 2
            || $tokens[$string]['line'] !== $tokens[$stackPtr]['line']
        ) {
            if ($tag === '@param') {
                $error = 'Missing param type and name with tag %s';
            } else {
                $error = 'Missing type with tag %s';
            }
            $data = [$tag];
            $phpcsFile->addError($error, $stackPtr, 'MissingType', $data);
            return;
        }

        $split = preg_split('/\s/', $tokens[$stackPtr + 2]['content'], 3);
        $this->type = array_shift($split);
        $this->description = trim(array_shift($split) ?: '') ?: null;

        if ($tag === '@return' && ! $this->processReturnTag($phpcsFile, $stackPtr)) {
            return;
        }

        if ($tag === '@param' && ! $this->processParamTag($phpcsFile, $stackPtr)) {
            return;
        }

        if ($tag === '@var' && ! $this->processVarTag($phpcsFile, $stackPtr)) {
            return;
        }

        if (! $this->isType($tag, $this->type)) {
            $error = 'Invalid type format with tag %s';
            $data = [$tag];
            $phpcsFile->addError($error, $stackPtr + 2, 'InvalidTypeFormat', $data);
            return;
        }

        if ($this->isThis($tag, $this->type)
            && strtolower($this->type) !== $this->type
        ) {
            $error = 'Invalid case of type with tag %s; expected "%s" but found "%s"';
            $data = [
                $tag,
                strtolower($this->type),
                $this->type,
            ];
            $fix = $phpcsFile->addFixableError($error, $stackPtr + 2, 'InvalidThisCase', $data);

            if ($fix) {
                $content = trim(strtolower($this->type) . ' ' . $this->description);
                $phpcsFile->fixer->replaceToken($stackPtr + 2, $content);
            }
        }

        // Type with tag contains only null, null[], null[][], null|null[], ...
        $cleared = array_unique(explode('|', strtolower(strtr($this->type, ['[' => '', ']' => '']))));
        if (count($cleared) === 1 && $cleared[0] === 'null') {
            $error = 'Type with tag %s contains only "null". Please specify all possible types';
            $data = [
                $tag,
                $this->type,
            ];
            $phpcsFile->addError($error, $stackPtr + 2, 'OnlyNullType', $data);
            return;
        }

        $this->checkTypes($phpcsFile, $tag, $stackPtr);
    }

    /**
     * @return bool True if can continue further processing.
     */
    private function processReturnTag(File $phpcsFile, int $tagPtr) : bool
    {
        if (strtolower($this->type) === 'void') {
            if ($this->description) {
                $error = 'Description for return "void" type is not allowed.'
                    . ' Please move it to method description.';
                $phpcsFile->addError($error, $tagPtr, 'ReturnVoidDescription');
                return false;
            }

            $error = 'Return tag with "void" type is redundant.';
            $fix = $phpcsFile->addFixableError($error, $tagPtr, 'ReturnVoid');

            if ($fix) {
                $this->removeTag($phpcsFile, $tagPtr);
            }

            return false;
        }

        if (isset($this->description[0]) && $this->description[0] === '$') {
            $error = 'Return tag description cannot start from variable name.';
            $phpcsFile->addError($error, $tagPtr + 2, 'ReturnVariable');
        }

        return true;
    }

    private function processParamTag(File $phpcsFile, int $tagPtr) : bool
    {
        $tokens = $phpcsFile->getTokens();

        $split = preg_split('/\s/', $tokens[$tagPtr + 2]['content'], 3);

        if (! isset($split[1])) {
            if ($this->isVariable($split[0])) {
                $error = 'Missing param type for param %s';
                $data = [
                    $split[0],
                ];
                $phpcsFile->addError($error, $tagPtr + 2, 'MissingParamType', $data);
            } else {
                $error = 'Missing parameter name in PHPDocs';
                $phpcsFile->addError($error, $tagPtr + 2, 'MissingParamName');
            }

            return false;
        }

        if (! $this->isVariable($split[1])) {
            $error = 'Invalid parameter name';
            $phpcsFile->addError($error, $tagPtr + 2, 'InvalidParamName');
            return false;
        }

        return true;
    }

    private function processVarTag(File $phpcsFile, int $tagPtr) : bool
    {
        $tokens = $phpcsFile->getTokens();

        $nested = 0;
        $commentStart = $phpcsFile->findPrevious(T_DOC_COMMENT_OPEN_TAG, $tagPtr - 1);
        $i = $tagPtr;
        while ($i = $phpcsFile->findPrevious(T_DOC_COMMENT_STRING, $i - 1, $commentStart)) {
            if ($tokens[$i]['content'][0] === '}') {
                --$nested;
            }

            if (substr($tokens[$i]['content'], -1) === '{') {
                ++$nested;
            }

            $i = $phpcsFile->findPrevious([T_DOC_COMMENT_TAG, T_DOC_COMMENT_OPEN_TAG], $i - 1);
        }

        $condition = end($tokens[$tagPtr]['conditions']);
        $isMemberVar = isset(Tokens::$ooScopeTokens[$condition]);

        $split = preg_split('/\s/', $tokens[$tagPtr + 2]['content'], 3);
        if ($nested > 0 || ! $isMemberVar) {
            if (! isset($split[1])) {
                if ($this->isVariable($split[0])) {
                    $error = 'Missing variable type';
                    $phpcsFile->addError($error, $tagPtr + 2, 'MissingVarType');
                } else {
                    $error = 'Missing variable name in PHPDocs';
                    $phpcsFile->addError($error, $tagPtr + 2, 'MissingVarName');
                }

                return false;
            }

            if (! $this->isVariable($split[1])) {
                $error = empty($split[1]) ? 'Missing variable name in PHPDocs' : 'Invalid variable name';
                $phpcsFile->addError($error, $tagPtr + 2, 'InvalidVarName');
                return false;
            }

            return true;
        }

        if (! empty($split[0][0]) && $split[0][0] === '$') {
            $error = 'Variable name should not be included in the tag';
            $fix = $phpcsFile->addFixableError($error, $tagPtr + 2, 'VariableName');

            if ($fix) {
                unset($split[0]);
                $content = trim(implode(' ', $split));
                if ($phpcsFile->getTokens()[$tagPtr + 3]['code'] !== T_DOC_COMMENT_WHITESPACE) {
                    $content .= ' ';
                }
                $phpcsFile->fixer->beginChangeset();
                if (trim($content) === '') {
                    $phpcsFile->fixer->replaceToken($tagPtr + 1, '');
                }
                $phpcsFile->fixer->replaceToken($tagPtr + 2, $content);
                $phpcsFile->fixer->endChangeset();
            }
            return false;
        }

        if (! empty($split[1][0]) && $split[1][0] === '$') {
            $error = 'Variable name should not be included in the tag';
            $fix = $phpcsFile->addFixableError($error, $tagPtr + 2, 'VariableName');

            if ($fix) {
                unset($split[1]);
                $phpcsFile->fixer->replaceToken($tagPtr + 2, implode(' ', $split));
            }
        }

        return true;
    }

    private function checkTypes(File $phpcsFile, string $tag, int $tagPtr) : void
    {
        $hasInvalidType = false;
        $this->types = explode('|', $this->type);

        // Check if types are unique.
        $uniq = array_unique($this->types);
        if ($uniq !== $this->types) {
            $expected = implode('|', $uniq);
            $error = 'Duplicated types with tag; expected "%s", but found "%s"';
            $data = [
                $expected,
                $this->type,
            ];
            $fix = $phpcsFile->addFixableError($error, $tagPtr + 2, 'DuplicateTypes', $data);

            if ($fix) {
                $content = trim($expected . ' ' . $this->description);
                $phpcsFile->fixer->replaceToken($tagPtr + 2, $content);
            }

            return;
        }

        $count = count($this->types);
        foreach ($this->types as $key => $type) {
            $lower = strtolower($type);

            if ($count > 1
                && ($lower === 'mixed' || strpos($lower, 'mixed[') === 0)
            ) {
                $error = 'Type %s cannot be mixed with other types.';
                $data = [
                    $type,
                ];
                $phpcsFile->addError($error, $tagPtr + 2, 'TypeMixed', $data);

                $hasInvalidType = true;
                continue;
            }

            $clearType = strtr($lower, ['[' => '', ']' => '']);
            if ($tag === '@param' || $tag === '@var') {
                if (in_array($clearType, ['void', 'true', 'false'], true)) {
                    $error = 'Invalid param type: "%s"';
                    $code = sprintf('InvalidType%s', ucfirst($clearType));
                    $data = [
                        $type,
                    ];
                    $phpcsFile->addError($error, $tagPtr + 2, $code, $data);

                    $hasInvalidType = true;
                    continue;
                }
            }

            // todo: what with void[] ?
            if ($clearType === 'void') {
                // If void is mixed up with other return types.
                $error = 'Type "void" is mixed with other types.';
                $phpcsFile->addError($error, $tagPtr + 2, 'VoidMixed');

                $hasInvalidType = true;
                continue;
            }

            if (in_array(strtolower($type), ['null', 'true', 'false'], true)) {
                $suggestedType = strtolower($type);
            } else {
                $suggestedType = $this->getSuggestedType($type);
            }
            if ($suggestedType !== $type) {
                if (strpos($suggestedType, 'self') === 0
                    && strtolower($type) !== $suggestedType
                ) {
                    if ($tag === '@param' || $tag === '@var') {
                        $error = 'The type cannot be class name. Please use "self" or "static" instead';
                    } else {
                        $error = 'Return type cannot be class name. Please use "self", "static" or "$this" instead'
                            . ' depends what you expect to be returned';
                    }
                    $phpcsFile->addError($error, $tagPtr + 2, 'InvalidReturnClassName');

                    $hasInvalidType = true;
                    continue;
                }

                $error = 'Invalid type with tag; expected "%s", but found "%s"';
                $data = [
                    $suggestedType,
                    $type,
                ];
                $fix = $phpcsFile->addFixableError($error, $tagPtr + 2, 'InvalidType', $data);

                if ($fix) {
                    $this->types[$key] = $suggestedType;
                    $content = trim(implode('|', $this->types) . ' ' . $this->description);
                    if ($phpcsFile->getTokens()[$tagPtr + 3]['code'] !== T_DOC_COMMENT_WHITESPACE) {
                        $content .= ' ';
                    }
                    $phpcsFile->fixer->replaceToken($tagPtr + 2, $content);
                }

                $hasInvalidType = true;
                continue;
            }
        }

        if ($hasInvalidType) {
            return;
        }

        // Check boolean values with tag
        $lowerReturnDocTypes = explode('|', strtolower($this->type));
        $hasTrue = in_array('true', $lowerReturnDocTypes, true);
        $hasFalse = in_array('false', $lowerReturnDocTypes, true);
        if (in_array('bool', $lowerReturnDocTypes, true)) {
            if ($hasTrue) {
                $error = 'Type with tag contains "bool" and "true". Please use just "bool"';
                $fix = $phpcsFile->addFixableError($error, $tagPtr + 2, 'BoolAndTrue');

                if ($fix) {
                    $types = array_filter($this->types, function ($v) {
                        return strtolower($v) !== 'true';
                    });
                    $content = trim(implode('|', $types) . ' ' . $this->description);
                    $phpcsFile->fixer->replaceToken($tagPtr + 2, $content);
                }

                return;
            }

            if ($hasFalse) {
                $error = 'Type with tag contains "bool" and "false". Please use just "bool"';
                $fix = $phpcsFile->addFixableError($error, $tagPtr + 2, 'BoolAndFalse');

                if ($fix) {
                    $types = array_filter($this->types, function ($v) {
                        return strtolower($v) !== 'false';
                    });
                    $content = trim(implode('|', $types) . ' ' . $this->description);
                    $phpcsFile->fixer->replaceToken($tagPtr + 2, $content);
                }

                return;
            }
        } elseif ($hasTrue && $hasFalse) {
            $error = 'Return tag contains "true" and "false". Please use "bool" instead.';
            $fix = $phpcsFile->addFixableError($error, $tagPtr + 2, 'TrueAndFalse');

            if ($fix) {
                $types = array_filter($this->types, function ($v) {
                    return ! in_array(strtolower($v), ['true', 'false'], true);
                });
                $types[] = 'bool';
                $content = trim(implode('|', $types) . ' ' . $this->description);
                $phpcsFile->fixer->replaceToken($tagPtr + 2, $content);
            }

            return;
        }

        // todo: here was previously uniqueness check

        // Check if order of types is as expected: first null, then simple types, and then complex.
        usort($this->types, function ($a, $b) {
            return $this->sortTypes($a, $b);
        });
        $content = implode('|', $this->types);
        if ($content !== $this->type) {
            $error = 'Invalid order of types with tag; expected "%s" but found "%s"';
            $data = [
                $content,
                $this->type,
            ];
            $fix = $phpcsFile->addFixableError($error, $tagPtr + 2, 'InvalidOrder', $data);

            if ($fix) {
                $content = trim($content . ' ' . $this->description);
                $phpcsFile->fixer->replaceToken($tagPtr + 2, $content);
            }
        }
    }
}

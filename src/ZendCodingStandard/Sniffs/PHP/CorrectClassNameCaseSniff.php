<?php

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use ZendCodingStandard\Helper\Namespaces;

use function array_map;
use function array_merge;
use function array_search;
use function explode;
use function get_declared_classes;
use function get_declared_interfaces;
use function get_declared_traits;
use function implode;
use function in_array;
use function ltrim;
use function preg_quote;
use function preg_replace;
use function strlen;
use function strtolower;
use function substr;
use function trim;

use const T_CLOSURE;
use const T_COLON;
use const T_COMMA;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_TAG;
use const T_DOC_COMMENT_WHITESPACE;
use const T_DOUBLE_COLON;
use const T_EXTENDS;
use const T_FUNCTION;
use const T_IMPLEMENTS;
use const T_NEW;
use const T_NS_SEPARATOR;
use const T_NULLABLE;
use const T_OPEN_PARENTHESIS;
use const T_PARENT;
use const T_RETURN_TYPE;
use const T_SELF;
use const T_STATIC;
use const T_STRING;
use const T_USE;
use const T_VARIABLE;
use const T_WHITESPACE;

/**
 * TODO: Better results for this sniff we will have if the parsed class is imported.
 * We can "include" the file on process, but probably it is not the best solution.
 */
class CorrectClassNameCaseSniff implements Sniff
{
    use Namespaces;

    /**
     * @var array
     */
    private $declaredClasses;

    public function __construct()
    {
        $this->declaredClasses = array_merge(
            get_declared_classes(),
            get_declared_interfaces(),
            get_declared_traits()
        );
    }

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [
            T_NEW,
            T_USE,
            T_DOUBLE_COLON,
            T_IMPLEMENTS,
            T_EXTENDS,
            // params of function/closures
            T_FUNCTION,
            T_CLOSURE,
            // return type (PHP 7)
            T_RETURN_TYPE,
            // PHPDocs tags
            T_DOC_COMMENT_TAG,
        ];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        switch ($tokens[$stackPtr]['code']) {
            case T_DOUBLE_COLON:
                $this->checkDoubleColon($phpcsFile, $stackPtr);
                return;
            case T_NEW:
                $this->checkNew($phpcsFile, $stackPtr);
                return;
            case T_USE:
                $this->checkUse($phpcsFile, $stackPtr);
                return;
            case T_FUNCTION:
            case T_CLOSURE:
                $this->checkFunctionParams($phpcsFile, $stackPtr);
                return;
            case T_RETURN_TYPE:
                $this->checkReturnType($phpcsFile, $stackPtr);
                return;
            case T_DOC_COMMENT_TAG:
                $this->checkTag($phpcsFile, $stackPtr);
                return;
        }

        $this->checkExtendsAndImplements($phpcsFile, $stackPtr);
    }

    /**
     * Checks statement before double colon - "ClassName::".
     */
    private function checkDoubleColon(File $phpcsFile, int $stackPtr) : void
    {
        $tokens = $phpcsFile->getTokens();

        $prevToken = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);

        // When "static::", "self::", "parent::" or "$var::", skip.
        if ($tokens[$prevToken]['code'] === T_STATIC
            || $tokens[$prevToken]['code'] === T_SELF
            || $tokens[$prevToken]['code'] === T_PARENT
            || $tokens[$prevToken]['code'] === T_VARIABLE
        ) {
            return;
        }

        $start = $phpcsFile->findPrevious(
            [T_NS_SEPARATOR, T_STRING],
            $prevToken - 1,
            null,
            true
        );

        $this->checkClass($phpcsFile, $start + 1, $prevToken + 1);//$prevToken - $start);
    }

    /**
     * Checks "new ClassName" statements.
     */
    private function checkNew(File $phpcsFile, int $stackPtr) : void
    {
        $tokens = $phpcsFile->getTokens();

        $nextToken = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);

        // When "new static", "new self" or "new $var", skip.
        if ($tokens[$nextToken]['code'] === T_STATIC
            || $tokens[$nextToken]['code'] === T_SELF
            || $tokens[$nextToken]['code'] === T_VARIABLE
        ) {
            return;
        }

        $end = $phpcsFile->findNext(
            [T_NS_SEPARATOR, T_STRING],
            $nextToken + 1,
            null,
            true
        );

        $this->checkClass($phpcsFile, $nextToken, $end);
    }

    /**
     * Checks "use" statements - global and traits.
     */
    private function checkUse(File $phpcsFile, int $stackPtr) : void
    {
        $tokens = $phpcsFile->getTokens();

        // Ignore USE keywords inside closures.
        $next = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);
        if ($tokens[$next]['code'] === T_OPEN_PARENTHESIS) {
            return;
        }

        $nextToken = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);

        $end = $phpcsFile->findNext(
            [T_NS_SEPARATOR, T_STRING],
            $nextToken + 1,
            null,
            true
        );

        // Global use statements.
        if (empty($tokens[$stackPtr]['conditions'])) {
            $this->checkClass($phpcsFile, $nextToken, $end, true);
            return;
        }

        // Traits.
        $this->checkClass($phpcsFile, $nextToken, $end);
    }

    /**
     * Checks params type hints
     */
    private function checkFunctionParams(File $phpcsFile, int $stackPtr) : void
    {
        $params = $phpcsFile->getMethodParameters($stackPtr);

        foreach ($params as $param) {
            if (! $param['type_hint']) {
                continue;
            }

            $end = $phpcsFile->findPrevious(Tokens::$emptyTokens, $param['token'] - 1, null, true);
            $before = $phpcsFile->findPrevious([T_COMMA, T_OPEN_PARENTHESIS, T_WHITESPACE], $end - 1);
            $first = $phpcsFile->findNext(Tokens::$emptyTokens, $before + 1, null, true);

            $this->checkClass($phpcsFile, $first, $end + 1);
        }
    }

    /**
     * Checks return type
     */
    private function checkReturnType(File $phpcsFile, int $stackPtr) : void
    {
        $before = $phpcsFile->findPrevious([T_COLON, T_NULLABLE], $stackPtr - 1);
        $first = $phpcsFile->findNext(Tokens::$emptyTokens, $before + 1, null, true);

        $this->checkClass($phpcsFile, $first, $stackPtr + 1);
    }

    /**
     * Checks PHPDocs tags
     */
    private function checkTag(File $phpcsFile, int $stackPtr) : void
    {
        $tokens = $phpcsFile->getTokens();

        if (! in_array($tokens[$stackPtr]['content'], ['@var', '@param', '@return', '@throws'], true)
            || $tokens[$stackPtr + 1]['code'] !== T_DOC_COMMENT_WHITESPACE
            || $tokens[$stackPtr + 2]['code'] !== T_DOC_COMMENT_STRING
        ) {
            return;
        }

        $string = $tokens[$stackPtr + 2]['content'];
        [$types] = explode(' ', $string);
        $typesArr = explode('|', $types);

        $newTypesArr = [];
        foreach ($typesArr as $type) {
            $expected = $this->getExpectedName($phpcsFile, $type, $stackPtr + 2);

            $newTypesArr[] = $expected;
        }

        $newTypes = implode('|', $newTypesArr);

        if ($newTypes !== $types) {
            $error = 'Invalid class name case: expected %s; found %s';
            $data = [
                $newTypes,
                $types,
            ];
            $fix = $phpcsFile->addFixableError($error, $stackPtr + 2, 'InvalidInPhpDocs', $data);

            if ($fix) {
                $phpcsFile->fixer->replaceToken(
                    $stackPtr + 2,
                    preg_replace('/^' . preg_quote($types) . '/', $newTypes, $string)
                );
            }
        }
    }

    /**
     * Returns expected class name for given $class.
     */
    private function getExpectedName(File $phpcsFile, string $class, int $stackPtr) : string
    {
        if ($class[0] === '\\') {
            $result = $this->hasDifferentCase(ltrim($class, '\\'));
            if ($result) {
                return '\\' . $result;
            }

            return $class;
        }

        $imports = $this->getGlobalUses($phpcsFile);

        // Check if class is imported.
        if (isset($imports[strtolower($class)])) {
            if ($imports[strtolower($class)]['alias'] !== $class) {
                return $imports[strtolower($class)]['alias'];
            }
        } else {
            // Class from the same namespace.
            $namespace = $this->getNamespace($phpcsFile, $stackPtr);
            $fullClassName = ltrim($namespace . '\\' . $class, '\\');

            $result = $this->hasDifferentCase(ltrim($fullClassName, '\\'));
            if ($result) {
                return ltrim(substr($result, strlen($namespace)), '\\');
            }
        }

        return $class;
    }

    /**
     * Checks "extends" and "implements" classes/interfaces.
     */
    private function checkExtendsAndImplements(File $phpcsFile, int $stackPtr) : void
    {
        $tokens = $phpcsFile->getTokens();

        $search = $stackPtr;
        while ($nextToken = $phpcsFile->findNext([T_WHITESPACE, T_COMMA], $search + 1, null, true)) {
            if ($tokens[$nextToken]['code'] !== T_NS_SEPARATOR
                && $tokens[$nextToken]['code'] !== T_STRING
            ) {
                break;
            }

            $end = $phpcsFile->findNext(
                [T_NS_SEPARATOR, T_STRING],
                $nextToken + 1,
                null,
                true
            );

            $this->checkClass($phpcsFile, $nextToken, $end);

            $search = $end;
        }
    }

    /**
     * Checks if class is used correctly.
     */
    private function checkClass(File $phpcsFile, int $start, int $end, bool $isGlobalUse = false) : void
    {
        $class = trim($phpcsFile->getTokensAsString($start, $end - $start));
        if ($class[0] === '\\') {
            $result = $this->hasDifferentCase(ltrim($class, '\\'));
            if ($result) {
                $this->error($phpcsFile, $start, $end, '\\' . $result, $class);
            }

            return;
        }

        if (! $isGlobalUse) {
            $imports = $this->getGlobalUses($phpcsFile);

            // Check if class is imported.
            if (isset($imports[strtolower($class)])) {
                if ($imports[strtolower($class)]['alias'] !== $class) {
                    $this->error($phpcsFile, $start, $end, $imports[strtolower($class)]['alias'], $class);
                }
            } else {
                // Class from the same namespace.
                $namespace = $this->getNamespace($phpcsFile, $start);
                $fullClassName = ltrim($namespace . '\\' . $class, '\\');

                $result = $this->hasDifferentCase(ltrim($fullClassName, '\\'));
                if ($result) {
                    $this->error($phpcsFile, $start, $end, ltrim(substr($result, strlen($namespace)), '\\'), $class);
                }
            }
        } else {
            // Global use statement.
            $result = $this->hasDifferentCase($class);
            if ($result) {
                $this->error($phpcsFile, $start, $end, $result, $class);
            }
        }
    }

    /**
     * Reports new fixable error.
     */
    private function error(File $phpcsFile, int $start, int $end, string $expected, string $actual) : void
    {
        $error = 'Invalid class name case: expected %s; found %s';
        $data = [
            $expected,
            $actual,
        ];
        $fix = $phpcsFile->addFixableError($error, $start + 1, 'Invalid', $data);

        if ($fix) {
            $phpcsFile->fixer->beginChangeset();
            for ($i = $start; $i < $end - 1; $i++) {
                $phpcsFile->fixer->replaceToken($i, '');
            }
            $phpcsFile->fixer->replaceToken($end - 1, $expected);
            $phpcsFile->fixer->endChangeset();
        }
    }

    /**
     * Checks if class is defined and has different case - then returns class name
     * with correct case. Otherwise returns false.
     */
    private function hasDifferentCase(string $class) : ?string
    {
        $index = array_search(strtolower($class), array_map('strtolower', $this->declaredClasses), true);

        if ($index === false) {
            // Not defined?
            return null;
        }

        if ($this->declaredClasses[$index] === $class) {
            // Exactly the same.
            return null;
        }

        return $this->declaredClasses[$index];
    }
}

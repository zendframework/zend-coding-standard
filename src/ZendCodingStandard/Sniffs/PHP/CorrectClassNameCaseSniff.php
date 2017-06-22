<?php
namespace ZendCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

/**
 * TODO: Better results for this sniff we will have if the parsed class is imported.
 * We can "include" the file on process, but probably it is not the best solution.
 */
class CorrectClassNameCaseSniff implements Sniff
{
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
     * @inheritDoc
     */
    public function register()
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
     * @inheritDoc
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
     *
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    private function checkDoubleColon(File $phpcsFile, $stackPtr)
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
     *
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    private function checkNew(File $phpcsFile, $stackPtr)
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
     *
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    private function checkUse(File $phpcsFile, $stackPtr)
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
     *
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    private function checkFunctionParams(File $phpcsFile, $stackPtr)
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
     * Checks return type (PHP 7)
     *
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    private function checkReturnType(File $phpcsFile, $stackPtr)
    {
        $before = $phpcsFile->findPrevious([T_COLON, T_NULLABLE], $stackPtr - 1);
        $first = $phpcsFile->findNext(Tokens::$emptyTokens, $before + 1, null, true);

        $this->checkClass($phpcsFile, $first, $stackPtr + 1);
    }

    /**
     * Checks PHPDocs tags
     *
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    private function checkTag(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (! in_array($tokens[$stackPtr]['content'], ['@var', '@param', '@return', '@throws'], true)
            || $tokens[$stackPtr + 1]['code'] !== T_DOC_COMMENT_WHITESPACE
            || $tokens[$stackPtr + 2]['code'] !== T_DOC_COMMENT_STRING
        ) {
            return;
        }

        $string = $tokens[$stackPtr + 2]['content'];
        list($types) = explode(' ', $string);
        $typesArr = explode('|', $types);

        $newTypesArr = [];
        foreach ($typesArr as $type) {
            $expected = $this->getExcepctedName($phpcsFile, $type, $stackPtr + 2);

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
     *
     * @param File $phpcsFile
     * @param string $class
     * @param int $stackPtr
     * @return string
     */
    private function getExcepctedName(File $phpcsFile, $class, $stackPtr)
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
     *
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    private function checkExtendsAndImplements(File $phpcsFile, $stackPtr)
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
     *
     * @param File $phpcsFile
     * @param int $start
     * @param int $end
     * @param bool $isGlobalUse
     * @return void
     */
    private function checkClass(File $phpcsFile, $start, $end, $isGlobalUse = false)
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
     *
     * @param File $phpcsFile
     * @param int $start
     * @param int $end
     * @param string $expected
     * @param string $actual
     * @return void
     */
    private function error(File $phpcsFile, $start, $end, $expected, $actual)
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
     * Returns array of imported classes. Key is lowercase name, and value is FQCN.
     *
     * @param File $phpcsFile
     * @return array
     */
    private function getGlobalUses(File $phpcsFile)
    {
        $tokens = $phpcsFile->getTokens();

        $imports = [];

        $use = 0;
        while ($use = $phpcsFile->findNext(T_USE, $use + 1)) {
            if (! empty($tokens[$use]['conditions'])) {
                continue;
            }

            $nextToken = $phpcsFile->findNext(T_WHITESPACE, $use + 1, null, true);

            $end = $phpcsFile->findNext(
                [T_NS_SEPARATOR, T_STRING],
                $nextToken + 1,
                null,
                true
            );

            $class = trim($phpcsFile->getTokensAsString($nextToken, $end - $nextToken));

            $endOfStatement = $phpcsFile->findEndOfStatement($use);
            if ($aliasStart = $phpcsFile->findNext([T_WHITESPACE, T_AS], $end + 1, $endOfStatement, true)) {
                $alias = trim($phpcsFile->getTokensAsString($aliasStart, $endOfStatement - $aliasStart));
            } else {
                if (strrchr($class, '\\') !== false) {
                    $alias = substr(strrchr($class, '\\'), 1);
                } else {
                    $alias = $class;
                }
            }

            $imports[strtolower($alias)] = ['alias' => $alias, 'class' => $class];
        }

        return $imports;
    }

    /**
     * Checks if class is defined and has different case - then returns class name
     * with correct case. Otherwise returns false.
     *
     * @param string $class
     * @return false|string
     */
    private function hasDifferentCase($class)
    {
        $index = array_search(strtolower($class), array_map('strtolower', $this->declaredClasses));

        if ($index === false) {
            // Not defined?
            return false;
        }

        if ($this->declaredClasses[$index] === $class) {
            // Exactly the same.
            return false;
        }

        return $this->declaredClasses[$index];
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return string
     */
    private function getNamespace(File $phpcsFile, $stackPtr)
    {
        if ($nsStart = $phpcsFile->findPrevious(T_NAMESPACE, $stackPtr - 1)) {
            $nsEnd = $phpcsFile->findNext([T_NS_SEPARATOR, T_STRING, T_WHITESPACE], $nsStart + 1, null, true);
            return trim($phpcsFile->getTokensAsString($nsStart + 1, $nsEnd - $nsStart - 1));
        }

        return '';
    }
}

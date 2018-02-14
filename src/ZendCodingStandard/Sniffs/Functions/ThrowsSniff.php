<?php

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use ZendCodingStandard\Helper\Methods;

use function array_reverse;
use function array_unique;
use function count;
use function in_array;
use function preg_split;
use function strtolower;
use function trim;

use const T_BITWISE_OR;
use const T_CATCH;
use const T_CLOSURE;
use const T_DOC_COMMENT_STRING;
use const T_FUNCTION;
use const T_NEW;
use const T_NS_SEPARATOR;
use const T_STRING;
use const T_THROW;
use const T_TRY;
use const T_VARIABLE;

class ThrowsSniff implements Sniff
{
    use Methods;

    /**
     * @var string[]
     */
    private $throwTags = [];

    /**
     * @var int[]
     */
    private $nameTokens = [T_NS_SEPARATOR, T_STRING];

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_FUNCTION];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $this->initScope($phpcsFile, $stackPtr);

        $this->throwTags = [];

        if ($commentStart = $this->getCommentStart($phpcsFile, $stackPtr)) {
            $this->processThrowsDoc($phpcsFile, $commentStart);
        }
        $this->processThrowStatements($phpcsFile, $stackPtr);
    }

    private function processThrowsDoc(File $phpcsFile, int $commentStart) : void
    {
        $tokens = $phpcsFile->getTokens();

        foreach ($tokens[$commentStart]['comment_tags'] as $pos => $tag) {
            if (strtolower($tokens[$tag]['content']) !== '@throws') {
                continue;
            }

            $exception = null;
            if ($tokens[$tag + 2]['code'] === T_DOC_COMMENT_STRING) {
                $split = preg_split('/\s/', $tokens[$tag + 2]['content'], 2);
                $exception = $split[0];
                $description = isset($split[1]) ? trim($split[1]) : null;
                $suggested = $this->getSuggestedType($exception);

                if ($exception !== $suggested) {
                    $error = 'Invalid exception type; expected %s, but found %s';
                    $data = [
                        $suggested,
                        $exception,
                    ];
                    $fix = $phpcsFile->addFixableError($error, $tag + 2, 'InvalidType', $data);

                    if ($fix) {
                        $content = trim($suggested . ' ' . $description);
                        $phpcsFile->fixer->replaceToken($tag + 2, $content);
                    }
                }

                $this->throwTags[$tag] = $suggested;
            }

            if (! $exception) {
                $error = 'Exception type missing for @throws tag in function comment';
                $phpcsFile->addError($error, $tag, 'MissingType');
            }
        }
    }

    protected function processThrowStatements(File $phpcsFile, int $stackPtr) : void
    {
        $tokens = $phpcsFile->getTokens();

        // Skip function without body
        if (! isset($tokens[$stackPtr]['scope_opener'])) {
            return;
        }

        $scopeBegin = $tokens[$stackPtr]['scope_opener'];
        $scopeEnd = $tokens[$stackPtr]['scope_closer'];

        $thrownExceptions = [];
        $thrownVariables = 0;
        $foundThrows = false;

        $throw = $scopeBegin;
        while (true) {
            $throw = $phpcsFile->findNext(T_THROW, $throw + 1, $scopeEnd);

            // Throw statement not found.
            if (! $throw) {
                break;
            }

            // The throw statement is in another scope.
            if (! $this->isLastScope($tokens[$throw]['conditions'], $stackPtr)) {
                continue;
            }

            $foundThrows = true;

            $next = $phpcsFile->findNext(Tokens::$emptyTokens, $throw + 1, null, true);
            if ($tokens[$next]['code'] === T_NEW) {
                $currException = $phpcsFile->findNext(Tokens::$emptyTokens, $next + 1, null, true);

                if (in_array($tokens[$currException]['code'], $this->nameTokens, true)) {
                    $end = $phpcsFile->findNext($this->nameTokens, $currException + 1, null, true);

                    $class = $phpcsFile->getTokensAsString($currException, $end - $currException);
                    $suggested = $this->getSuggestedType($class);

                    if ($class !== $suggested) {
                        $error = 'Invalid exception class name; expected %s, but found %s';
                        $data = [
                            $suggested,
                            $class,
                        ];
                        $fix = $phpcsFile->addFixableError($error, $currException, 'InvalidExceptionClassName', $data);

                        if ($fix) {
                            $phpcsFile->fixer->beginChangeset();
                            $phpcsFile->fixer->replaceToken($currException, $suggested);
                            for ($i = $currException + 1; $i < $end; ++$i) {
                                $phpcsFile->fixer->replaceToken($i, '');
                            }
                            $phpcsFile->fixer->endChangeset();
                        }
                    }

                    $thrownExceptions[] = $suggested;
                    continue;
                }
            } elseif ($tokens[$next]['code'] === T_VARIABLE) {
                $catch = $phpcsFile->findPrevious(T_CATCH, $throw, $scopeBegin);

                if ($catch) {
                    $thrownVar = $phpcsFile->findPrevious(
                        T_VARIABLE,
                        $tokens[$catch]['parenthesis_closer'] - 1,
                        $tokens[$catch]['parenthesis_opener']
                    );

                    if ($tokens[$thrownVar]['content'] === $tokens[$next]['content']) {
                        $exceptions = $this->getExceptions(
                            $phpcsFile,
                            $tokens[$catch]['parenthesis_opener'] + 1,
                            $thrownVar - 1
                        );

                        foreach ($exceptions as $exception) {
                            $thrownExceptions[] = $exception;
                        }
                    }

                    continue;
                }
            }

            ++$thrownVariables;
        }

        if (! $foundThrows) {
            // It should be disabled if we want to declare implicit throws
            foreach ($this->throwTags as $ptr => $class) {
                $error = 'Function does not throw any exception but has @throw tag';
                $phpcsFile->addError($error, $ptr, 'AdditionalThrowTag');
            }

            return;
        }

        // Only need one @throws tag for each type of exception thrown.
        $thrownExceptions = array_unique($thrownExceptions);

        // Make sure @throws tag count matches thrown count.
        $thrownCount = count($thrownExceptions) ?: 1;
        $tagCount = count(array_unique($this->throwTags));

        if ($thrownVariables > 0) {
            if ($thrownCount > $tagCount) {
                $error = 'Expected at least %d @throws tag(s) in function comment; %d found';
                $data = [
                    $thrownCount,
                    $tagCount,
                ];
                $phpcsFile->addError($error, $stackPtr, 'WrongNumberAtLeast', $data);
                return;
            }
        } else {
            if ($thrownCount !== $tagCount) {
                $error = 'Expected %d @throws tag(s) in function comment; %d found';
                $data = [
                    $thrownCount,
                    $tagCount,
                ];
                $phpcsFile->addError($error, $stackPtr, 'WrongNumberExact', $data);
                return;
            }
        }

        foreach ($thrownExceptions as $throw) {
            if (! in_array($throw, $this->throwTags, true)) {
                $error = 'Missing @throws tag for "%s" exception';
                $data = [$throw];
                $phpcsFile->addError($error, $stackPtr, 'Missing', $data);
            }
        }
    }

    /**
     * @return string[]
     */
    private function getExceptions(File $phpcsFile, int $from, int $to) : array
    {
        $tokens = $phpcsFile->getTokens();

        $exceptions = [];
        $currName = '';
        $start = null;
        $end = null;

        for ($i = $from; $i <= $to; ++$i) {
            if (in_array($tokens[$i]['code'], $this->nameTokens, true)) {
                if ($currName === '') {
                    $start = $i;
                }

                $end = $i;
                $currName .= $tokens[$i]['content'];
            }

            if ($tokens[$i]['code'] === T_BITWISE_OR || $i === $to) {
                $suggested = $this->getSuggestedType($currName);

                if ($suggested !== $currName) {
                    $error = 'Invalid exception class name in catch; expected %s, but found %s';
                    $data = [
                        $suggested,
                        $currName,
                    ];
                    $fix = $phpcsFile->addFixableError($error, $start, 'InvalidCatchClassName', $data);

                    if ($fix) {
                        $phpcsFile->fixer->beginChangeset();
                        $phpcsFile->fixer->replaceToken($start, $suggested);
                        for ($j = $start + 1; $j <= $end; ++$j) {
                            $phpcsFile->fixer->replaceToken($j, '');
                        }
                        $phpcsFile->fixer->endChangeset();
                    }
                }

                $exceptions[] = $suggested;
                $currName = '';
                $start = null;
                $end = null;
            }
        }

        return $exceptions;
    }

    /**
     * Check if $scope is the last closure/function/try condition.
     *
     * @param string[] $conditions
     * @param int $scope Scope to check in conditions.
     */
    private function isLastScope(array $conditions, int $scope) : bool
    {
        foreach (array_reverse($conditions, true) as $ptr => $code) {
            if ($code !== T_FUNCTION && $code !== T_CLOSURE && $code !== T_TRY) {
                continue;
            }

            return $ptr === $scope;
        }

        return false;
    }
}

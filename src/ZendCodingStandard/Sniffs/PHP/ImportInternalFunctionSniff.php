<?php

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use ZendCodingStandard\Helper\Namespaces;

use function array_flip;
use function get_defined_functions;
use function sprintf;
use function strtolower;

use const T_BITWISE_AND;
use const T_DOUBLE_COLON;
use const T_FUNCTION;
use const T_NAMESPACE;
use const T_NEW;
use const T_NS_SEPARATOR;
use const T_OBJECT_OPERATOR;
use const T_OPEN_PARENTHESIS;
use const T_OPEN_TAG;
use const T_STRING;

class ImportInternalFunctionSniff implements Sniff
{
    use Namespaces;

    /**
     * @var array Hash map of all php built in function names.
     */
    private $builtInFunctions;

    /**
     * @var File Currently processed file.
     */
    private $currentFile;

    /**
     * @var string Currently processed namespace.
     */
    private $currentNamespace;

    /**
     * @var array Array of imported function in current namespace.
     */
    private $importedFunctions;

    /**
     * @var null|int Last use statement position.
     */
    private $lastUse;

    public function __construct()
    {
        $allFunctions = get_defined_functions();
        $this->builtInFunctions = array_flip($allFunctions['internal']);
    }

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_STRING];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->currentFile !== $phpcsFile) {
            $this->currentFile = $phpcsFile;
            $this->currentNamespace = null;
        }

        $namespace = $this->getNamespace($phpcsFile, $stackPtr);
        if ($namespace && $this->currentNamespace !== $namespace) {
            $this->currentNamespace = $namespace;
            $this->importedFunctions = $this->getImportedFunctions($phpcsFile, $stackPtr, $this->lastUse);
        }

        $tokens = $phpcsFile->getTokens();

        // Make sure this is a function call.
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true);
        if (! $next || $tokens[$next]['code'] !== T_OPEN_PARENTHESIS) {
            return;
        }

        $content = strtolower($tokens[$stackPtr]['content']);
        if (! isset($this->builtInFunctions[$content])) {
            return;
        }

        $prev = $phpcsFile->findPrevious(
            Tokens::$emptyTokens + [T_BITWISE_AND => T_BITWISE_AND, T_NS_SEPARATOR => T_NS_SEPARATOR],
            $stackPtr - 1,
            null,
            true
        );
        if ($tokens[$prev]['code'] === T_FUNCTION
            || $tokens[$prev]['code'] === T_NEW
            || $tokens[$prev]['code'] === T_STRING
            || $tokens[$prev]['code'] === T_DOUBLE_COLON
            || $tokens[$prev]['code'] === T_OBJECT_OPERATOR
        ) {
            return;
        }

        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $stackPtr - 1, null, true);
        if ($tokens[$prev]['code'] === T_NS_SEPARATOR) {
            if (! $namespace) {
                $error = 'FQN for PHP internal function "%s" is not needed here, file does not have defined namespace';
                $data = [
                    $content,
                ];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'NoNamespace', $data);
                if ($fix) {
                    $phpcsFile->fixer->replaceToken($prev, '');
                }
            } elseif (isset($this->importedFunctions[$content])) {
                if (strtolower($this->importedFunctions[$content]['fqn']) === $content) {
                    $error = 'FQN for PHP internal function "%s" is not needed here, function is already imported';
                    $data = [
                        $content,
                    ];

                    $fix = $phpcsFile->addFixableError($error, $stackPtr, 'RedundantFQN', $data);
                    if ($fix) {
                        $phpcsFile->fixer->replaceToken($prev, '');
                    }
                }
            } else {
                $error = 'PHP internal function "%s" must be imported';
                $data = [
                    $content,
                ];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'ImportFQN', $data);
                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->replaceToken($prev, '');
                    $this->importFunction($phpcsFile, $stackPtr, $content);
                    $phpcsFile->fixer->endChangeset();
                }
            }
        } elseif ($namespace) {
            if (! isset($this->importedFunctions[$content])) {
                $error = 'PHP internal function "%s" must be imported';
                $data = [
                    $content,
                ];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Import', $data);
                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();
                    $this->importFunction($phpcsFile, $stackPtr, $content);
                    $phpcsFile->fixer->endChangeset();
                }
            }
        }
    }

    private function importFunction(File $phpcsFile, int $stackPtr, string $functionName) : void
    {
        if ($this->lastUse) {
            $ptr = $phpcsFile->findEndOfStatement($this->lastUse);
        } else {
            $nsStart = $phpcsFile->findPrevious(T_NAMESPACE, $stackPtr);
            if ($nsStart) {
                $tokens = $phpcsFile->getTokens();
                if (isset($tokens[$nsStart]['scope_opener'])) {
                    $ptr = $tokens[$nsStart]['scope_opener'];
                } else {
                    $ptr = $phpcsFile->findEndOfStatement($nsStart);
                    $phpcsFile->fixer->addNewline($ptr);
                }
            } else {
                $ptr = $phpcsFile->findPrevious(T_OPEN_TAG, $stackPtr - 1);
            }
        }

        $phpcsFile->fixer->addNewline($ptr);
        $phpcsFile->fixer->addContent($ptr, sprintf('use function %s;', $functionName));
        if (! $this->lastUse && (! $nsStart || isset($tokens[$nsStart]['scope_opener']))) {
            $phpcsFile->fixer->addNewline($ptr);
        }

        $this->importedFunctions[$functionName] = [
            'name' => $functionName,
            'fqn' => $functionName,
        ];
    }
}

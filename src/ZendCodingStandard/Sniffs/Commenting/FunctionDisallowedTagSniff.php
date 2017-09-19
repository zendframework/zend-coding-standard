<?php
namespace ZendCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use function array_filter;
use function current;
use function key;
use function sprintf;
use function strtolower;
use function substr;
use function ucfirst;

use const ARRAY_FILTER_USE_KEY;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_FUNCTION;
use const T_WHITESPACE;

class FunctionDisallowedTagSniff implements Sniff
{
    /**
     * Disallowed tags. Key is tag name and value is extra message,
     * which will be shown on error.
     *
     * @var string[]
     */
    public $disallowedTags = [
        '@author' => 'Information about the author will be found with the commit.',
        '@copyright' => 'Please see copyright notes on the top of the file.',
        '@license' => 'Please see license notes on the top of the file.',
        '@package' => '',
        '@subpackage' => '',
        '@version' => '',
        '@inheritDoc' => 'Please define explicitly params, return type and throws for the method.',
        '@expectedException' => 'Please use appropriate method instead just before call'
            . ' which should throw the exception.',
        '@expectedExceptionCode' => 'Please use appropriate method instead just before call'
            . ' which should throw the exception.',
        '@expectedExceptionMessage' => 'Please use appropriate method instead just before call'
            . ' which should throw the exception.',
        '@expectedExceptionMessageRegExp' => 'Please use appropriate method instead just before call'
            . ' which should throw the exception.',
    ];

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
        $tokens = $phpcsFile->getTokens();
        $skip = Tokens::$methodPrefixes
            + [T_WHITESPACE => T_WHITESPACE];

        $commentEnd = $phpcsFile->findPrevious($skip, $stackPtr - 1, null, true);
        // There is no doc-comment for the function.
        if ($tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG) {
            return;
        }

        $commentStart = $tokens[$commentEnd]['comment_opener'];
        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            $content = strtolower($tokens[$tag]['content']);
            $result = array_filter($this->disallowedTags, function ($key) use ($content) {
                return strtolower($key) === $content;
            }, ARRAY_FILTER_USE_KEY);

            if (! $result) {
                continue;
            }

            $tagName = key($result);
            $tagError = current($result);
            $error = 'Tag %s is not allowed. %s';
            $errorCode = sprintf('%sTagNotAllowed', ucfirst(substr($tagName, 1)));
            $data = [
                $tagName,
                $tagError,
            ];

            $phpcsFile->addError($error, $tag, $errorCode, $data);
        }
    }
}

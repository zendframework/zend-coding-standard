<?php

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use function sprintf;
use function strtolower;
use function ucfirst;

use const T_DOC_COMMENT_TAG;

class TagCaseSniff implements Sniff
{
    /**
     * @var string[]
     */
    public $tags = [
        // PHPDocs tags
        '@api' => '@api',
        '@author' => '@author',
        '@category' => '@category',
        '@copyright' => '@copyright',
        '@deprecated' => '@deprecated',
        '@example' => '@example',
        '@filesource' => '@filesource',
        '@global' => '@global',
        '@ignore' => '@ignore',
        '@inheritdoc' => '@inheritDoc',
        '@internal' => '@internal',
        '@license' => '@license',
        '@link' => '@link',
        '@method' => '@method',
        '@package' => '@package',
        '@param' => '@param',
        '@property' => '@property',
        '@property-read' => '@property-read',
        '@property-write' => '@property-write',
        '@return' => '@return',
        '@see' => '@see',
        '@since' => '@since',
        '@source' => '@source',
        '@subpackage' => '@subpackage',
        '@throws' => '@throws',
        '@todo' => '@todo',
        '@uses' => '@uses',
        '@used-by' => '@used-by',
        '@var' => '@var',
        '@version' => '@version',
        // PHPUnit annotations
        '@after' => '@after',
        '@afterclass' => '@afterClass',
        '@backupglobals' => '@backupGlobals',
        '@backupstaticattributes' => '@backupStaticAttributes',
        '@before' => '@before',
        '@beforeclass' => '@beforeClass',
        '@codecoverageignore' => '@codeCoverageIgnore',
        '@codecoverageignorestart' => '@codeCoverageIgnoreStart',
        '@codecoverageignoreend' => '@codeCoverageIgnoreEnd',
        '@covers' => '@covers',
        '@coversdefaultclass' => '@coversDefaultClass',
        '@coversnothing' => '@coversNothing',
        '@dataprovider' => '@dataProvider',
        '@depends' => '@depends',
        '@expectedexception' => '@expectedException',
        '@expectedexceptioncode' => '@expectedExceptionCode',
        '@expectedexceptionmessage' => '@expectedExceptionMessage',
        '@expectedexceptionmessageregexp' => '@expectedExceptionMessageRegExp',
        '@group' => '@group',
        '@large' => '@large',
        '@medium' => '@medium',
        '@preserveglobalstate' => '@preserveGlobalState',
        '@requires' => '@requires',
        '@runtestsinseparateprocesses' => '@runTestsInSeparateProcesses',
        '@runinseparateprocess' => '@runInSeparateProcess',
        '@small' => '@small',
        '@test' => '@test',
        '@testdox' => '@testdox',
        '@ticket' => '@ticket',
    ];

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_DOC_COMMENT_TAG];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $content = $tokens[$stackPtr]['content'];
        $lower = strtolower($content);
        if (! isset($this->tags[$lower])) {
            return;
        }

        if ($this->tags[$lower] === $content) {
            return;
        }

        $tagName = $this->tags[$lower];
        $error = 'Invalid case tag. Expected "%s", but found "%s"';
        $errorCode = sprintf('%sTagWrongCase', ucfirst($tagName));
        $data = [
            $tagName,
            $content,
        ];

        $phpcsFile->addError($error, $stackPtr, $errorCode, $data);
    }
}

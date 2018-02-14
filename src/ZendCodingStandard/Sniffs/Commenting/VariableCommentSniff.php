<?php
/**
 * Copied from Squiz_Sniffs_Commenting_VariableCommentSniff
 * with modifications:
 *  - use suggested types from ZendCodingStandard\CodingStandard
 *  - skip normal comments if there is one blank line after the comment
 */

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use ZendCodingStandard\CodingStandard;

use const T_COMMENT;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_STRING;
use const T_PRIVATE;
use const T_PROTECTED;
use const T_PUBLIC;
use const T_STATIC;
use const T_VAR;
use const T_WHITESPACE;

class VariableCommentSniff extends AbstractVariableSniff
{
    /**
     * @param int $stackPtr
     */
    protected function processMemberVar(File $phpcsFile, $stackPtr) : void
    {
        $tokens = $phpcsFile->getTokens();
        $ignore = [
            T_PUBLIC,
            T_PRIVATE,
            T_PROTECTED,
            T_VAR,
            T_STATIC,
            T_WHITESPACE,
        ];

        $commentEnd = $phpcsFile->findPrevious($ignore, $stackPtr - 1, null, true);
        if ($commentEnd === false
            || ($tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG
                && $tokens[$commentEnd]['code'] !== T_COMMENT)
        ) {
            $phpcsFile->addError('Missing member variable doc comment', $stackPtr, 'Missing');
            return;
        }

        if ($tokens[$commentEnd]['code'] === T_COMMENT) {
            if ($tokens[$commentEnd]['line'] === $tokens[$stackPtr]['line'] - 1) {
                $error = 'You must use "/**" style comments for a member variable comment';
                $phpcsFile->addError($error, $stackPtr, 'WrongStyle');
            }

            return;
        }

        $commentStart = $tokens[$commentEnd]['comment_opener'];

        $foundVar = null;
        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            if ($tokens[$tag]['content'] === '@var') {
                if ($foundVar !== null) {
                    $error = 'Only one @var tag is allowed in a member variable comment';
                    $phpcsFile->addError($error, $tag, 'DuplicateVar');
                } else {
                    $foundVar = $tag;
                }
            } elseif ($tokens[$tag]['content'] === '@see') {
                // Make sure the tag isn't empty.
                $string = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $tag, $commentEnd);
                if ($string === false || $tokens[$string]['line'] !== $tokens[$tag]['line']) {
                    $error = 'Content missing for @see tag in member variable comment';
                    $phpcsFile->addError($error, $tag, 'EmptySees');
                }
            } else {
                $error = '%s tag is not allowed in member variable comment';
                $data = [$tokens[$tag]['content']];
                $phpcsFile->addWarning($error, $tag, 'TagNotAllowed', $data);
            }
        }

        // The @var tag is the only one we require.
        if ($foundVar === null) {
            $error = 'Missing @var tag in member variable comment';
            $phpcsFile->addError($error, $commentEnd, 'MissingVar');
            return;
        }

        $firstTag = $tokens[$commentStart]['comment_tags'][0];
        if ($foundVar !== null && $tokens[$firstTag]['content'] !== '@var') {
            $error = 'The @var tag must be the first tag in a member variable comment';
            $phpcsFile->addError($error, $foundVar, 'VarOrder');
        }

        // Make sure the tag isn't empty and has the correct padding.
        $string = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $foundVar, $commentEnd);
        if ($string === false || $tokens[$string]['line'] !== $tokens[$foundVar]['line']) {
            $error = 'Content missing for @var tag in member variable comment';
            $phpcsFile->addError($error, $foundVar, 'EmptyVar');
            return;
        }

        $varType = $tokens[$foundVar + 2]['content'];
        // changed to use bool and int instead of boolean and integer accordingly
        $suggestedType = CodingStandard::suggestType($varType);
        if ($varType !== $suggestedType) {
            $error = 'Expected "%s" but found "%s" for @var tag in member variable comment';
            $data = [
                $suggestedType,
                $varType,
            ];
            $fix = $phpcsFile->addFixableError($error, $foundVar + 2, 'IncorrectVarType', $data);

            if ($fix) {
                $phpcsFile->fixer->replaceToken($foundVar + 2, $suggestedType);
            }
        }
    }

    /**
     * @param int $stackPtr
     */
    protected function processVariable(File $phpcsFile, $stackPtr) : void
    {
        // Sniff process only class member vars.
    }

    /**
     * @param int $stackPtr
     */
    protected function processVariableInString(File $phpcsFile, $stackPtr) : void
    {
        // Sniff process only class member vars.
    }
}

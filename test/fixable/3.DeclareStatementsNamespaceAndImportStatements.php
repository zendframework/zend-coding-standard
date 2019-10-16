<?php

/**
 * This file contains an example of coding styles.
 */

declare(strict_types=1);

namespace   ZendCodingStandardTest  \  fixed;

use Bar\Baz;
use \DateTimeZone;
use function strrev;
use \DateInterval;
use DateTimeImmutable;

/**
 * FooBar is an example class.
 */
class DeclareStatementsNamespaceAndImportStatements
{
    public function testFilesFileHeader(): void
    {
        // The header of a PHP file may consist of a number of different
        // blocks. If present, each of the blocks below MUST be separated
        // by a single blank line, and MUST NOT contain a blank line. Each
        // block MUST be in the order listed below, although blocks that
        // are not relevant may be omitted.
        //
        // Opening php tag.
        // File-level docblock.
        // One or more declare statements.
        // The namespace declaration of the file.
        // One or more class-based use import statements.
        // One or more function-based use import statements.
        // One or more constant-based use import statements.
        // The remainder of the code in the file.
    }

    public function testNamespaceSpacing(): void
    {
        // There MUST be a single space after the namespace keyword.
        //
        // There MAY NOT be a space around a namespace separator.
    }

    public function testImportStatements(): void
    {
        // Import statements MUST never begin with a leading backslash as they
        // must always be fully qualified.
        //
        // Compound namespaces with a depth of more than two MUST NOT be used.
        // Import statements must be alphabetically sorted.
        //
        // Functions and const keywords must be lowercase in import statements.
        //
        // Unused import statements are not allowed.
        //
        // Superfluous leading backslash in import statements MUST be removed.
        //
        // Fancy group import statements are not allowed.
        //
        // Each import statements MUST be on its own line.
        //
        // Import statements must be grouped (classes, functions, constants)
        // and MUST be separated by empty lines.
        //
        // Import statements aliases for classes, traits, functions and
        // constants MUST be useful.
        //
        // Classes, traits, interfaces, constants and functions MUST be imported.
        //
        // Internal functions MUST be imported.
        //
        // Internal constants MUST be imported.

        strrev(
            (new DateTimeImmutable('@' . time(), new DateTimeZone('UTC')))
                ->sub(new DateInterval('P1D'))
                ->format(DATE_RFC3339)
        );

        new Baz();
    }

    public function testDeclareStatement(): void
    {
        // When wishing to declare strict types in files containing markup
        // outside PHP opening and closing tags, the declaration MUST be on the
        // first line of the file and include an opening PHP tag, the strict
        // types declaration and closing tag.
        //
        // Declare statements MUST contain no spaces and MUST be exactly
        // declare(strict_types=1) (with an optional semi-colon terminator).
        //
        // Block declare statements are allowed and MUST be formatted as below.

        declare(ticks=1)
        {
            // some code
        }
    }
}

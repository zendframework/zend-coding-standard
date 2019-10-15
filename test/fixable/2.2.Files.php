<?php

namespace ZendCodingStandardTest\fixed;

class Files
{
    public function testLineEndings(): void
    {
        // All PHP files MUST use the Unix LF (linefeed) line ending only.
    }

    public function testEndFileNewline(): void
    {
        // All PHP files MUST end with a non-blank line, terminated with a single LF.
    }

    public function testClosingTag(): void
    {
        // The php closing tag MUST be omitted from files containing only PHP.
    }
}



?>

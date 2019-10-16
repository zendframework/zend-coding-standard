<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\fixed;

use ArrayAccess;
use Countable;
use ParentClass;
use Serializable;

class ExtendsAndImplements extends ParentClass implements
    ArrayAccess,
    Countable,
    Serializable
{
    public function testClassDeclaration()
    {
        // The extends and implements keywords MUST be declared on the same line
        // as the class name.
        //
        // The opening brace for the class MUST go on its own line; the closing
        // brace for the class MUST go on the next line after the body.
        //
        // Opening braces MUST be on their own line and MUST NOT be preceded or
        // followed by a blank line.
        //
        // Closing braces MUST be on their own line and MUST NOT be preceded by a
        // blank line.
        //
        // Lists of implements and, in the case of interfaces, extends MAY be
        // split across multiple lines, where each subsequent line is indented
        // once. When doing so, the first item in the list MUST be on the next
        // line, and there MUST be only one interface per line.
    }
}

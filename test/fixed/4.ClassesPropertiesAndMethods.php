<?php

namespace ZendCodingStandardTest\fixed;

use Foo;

class ClassesPropertiesAndMethods
{
    public function testClassClosingBrace(): void
    {
        // Any closing brace MUST NOT be followed by any comment or statement on
        // the same line.
        //
        // NOTE: Fixers are not available for this sniff as it is likely that
        // comments would be found more than anything else, and simply moving
        // them to the next line is probably not the right fix. More likely,
        // the comment should be removed, which only the developer should do.
    }

    public function testClassInstantiation(): void
    {
        // When instantiating a new class, parentheses MUST always be present
        // even when there are no arguments passed to the constructor.

        new Foo();
    }
}

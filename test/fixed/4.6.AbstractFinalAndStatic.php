<?php

namespace ZendCodingStandardTest\fixed;

abstract class AbstractFinalAndStatic
{
    protected static $foo;

    public function testMethodDeclarations(): void
    {
        // When present, the abstract and final declarations MUST precede the
        // visibility declaration.
    }

    abstract protected function testAbstractDeclarations(): void;

    final public static function testStaticDeclaration(): void
    {
        // When present, the static declaration MUST come after the visibility
        // declaration.
        //
        // The final keyword on methods MUST be omitted in final classes.
    }
}

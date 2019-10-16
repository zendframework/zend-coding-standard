<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\fixed;

abstract class AbstractFinalAndStatic
{
    static protected $foo;

    public function testMethodDeclarations(): void
    {
        // When present, the abstract and final declarations MUST precede the
        // visibility declaration.
    }

    protected abstract function testAbstractDeclarations(): void;

    final static public function testStaticDeclaration(): void
    {
        // When present, the static declaration MUST come after the visibility
        // declaration.
        //
        // The final keyword on methods MUST be omitted in final classes.
    }
}

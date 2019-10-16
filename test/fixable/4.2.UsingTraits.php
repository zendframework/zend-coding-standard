<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\fixed;

use Vendor\Package\ThirdTrait;
use Vendor\Package\SecondTrait;
use Vendor\Package\FirstTrait;

class UsingTraits
{
    use ThirdTrait;

    use FirstTrait, SecondTrait;

    private $property;

    public function testTraitsUseDeclaration(): void
    {
        // The use keyword used inside the classes to implement traits MUST be
        // declared on the next line after the opening brace.
        //
        // Each individual trait that is imported into a class MUST be included
        // one-per-line and each inclusion MUST have its own use import
        // statement.
        //
        // When the class has nothing after the use import statement, the class
        // closing brace MUST be on the next line after the use import statement.
        // Otherwise, it MUST have a blank line after the use import statement.
        // When using the insteadof and as operators they must be used as follows
        // taking note of indentation, spacing, and new lines.
        //
        // Traits MUST be sorted alphabetically.
    }
}

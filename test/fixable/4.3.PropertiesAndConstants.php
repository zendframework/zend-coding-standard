<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\fixed;

class PropertiesAndConstants
{
    public const VAR = 'foo';

    private const TEST = 'bar!';

    public $foo = null;

    public static int $bar = 0;

    private $baz = null;

    public function testPropertyDeclaration(): void
    {
        // Visibility MUST be declared on all properties.
        //
        // The var keyword MUST NOT be used to declare a property.
        //
        // There MUST NOT be more than one property declared per statement.
        //
        // Property names MUST NOT be prefixed with a single underscore to
        // indicate protected or private visibility. That is, an underscore
        // prefix explicitly has no meaning.
        //
        // There MUST be a space between type declaration and property name.
        //
        // Default null values MUST be omitted for class properties.
    }

    public function testConstantVisibility(): void
    {
        // Visibility MUST be declared on all constants if your project PHP
        // minimum version supports constant visibilities (PHP 7.1 or later).
    }
}

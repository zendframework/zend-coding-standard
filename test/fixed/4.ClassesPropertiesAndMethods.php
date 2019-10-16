<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\fixed;

use ArrayObject;
use DateTime;
use Foo;
use PHP_CodeSniffer\Config;
use stdClass;
use Vendor\FooException;
use Vendor\FooInterface;
use Vendor\FooTrait;

use function get_class;

abstract class AbstractFoo
{
}

class ClassesPropertiesAndMethods extends AbstractFoo implements FooInterface
{
    use FooTrait;

    public function __construct()
    {
    }

    public function testDuplicateClassNames(): void
    {
        // There MAY NOT be duplicate class names.
    }

    public function testConstructor(): void
    {
        // PHP 4 style constructors SHOULD NOT be used.
    }

    public function testClassPrefixAndSuffix(): void
    {
        // Abstract classes MUST have a `Abstract` prefix.
        // Exception classes MUST have a `Exception` suffix.
        // Interface classes MUST have a `Interface` suffix.
        // Trait classes MUST have a `Trait` suffix.

        throw new FooException('Oops!');
    }

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

    public function testCorrectClassNames(): void
    {
        // The correct class names MUST be used.

        new DateTime();

        new ArrayObject();
        new ArrayObject();

        DateTime::createFromFormat('Y');
    }

    public function testClassNameResolution(): iterable
    {
        // For self-reference a class lower-case `self::` MUST be used without
        // spaces around the scope resolution operator.
        //
        // Class name resolution via `::class` MUST be used instead of
        // `__CLASS__`, `get_class()`, `get_class($this)`,
        // `get_called_class()` and `get_parent_class()`.

        yield self::class;
        yield self::class;
        yield static::class;
        yield get_class(new stdClass());
        yield parent::class;
        yield static::class;

        $class = Config::class;
    }

    public function testThereMayNotBeAnyWhitespaceAroundTheDoubleColon(): void
    {
        // There MAY NOT be any whitespace around the double colon operator.

        DateTime::createFromFormat('Y-m-d', '2016-01-01');

        DateTime::createFromFormat('Y-m-d', '2016-01-01');

        DateTime::createFromFormat('Y-m-d', '2016-01-01');
    }

    public function testUnusedPrivateMethods(): void
    {
        // All private methods, constants and properties MUST be used.
    }
}

<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\fixed;

use Bar\Baz;
use DateTimeImmutable;
use DateTimeZone;

use function chop;
use function compact;
use function extract;
use function is_null;
use function settype;
use function sizeof;
use function strtolower;
use function time;
use function var_dump;

use const PHP_SAPI;

class BasicCodingStandard extends Bar implements FooInterface
{
    public function sampleFunction(int $a, ?int $b = null): array
    {
        if ($a === $b) {
            new Baz();
        } elseif ($a > $b) {
            $foo->bar($arg1);
        } else {
            new DateTimeImmutable('@' . time(), new DateTimeZone('UTC'));
        }
    }

    final public static function bar()
    {
        // method body
    }

    public function testThereMayNotBeAnyContentBeforeTheOpeningTag(): void
    {
        // There MAY NOT be any content before the opening tag.
    }

    public function testTheShortOpenTagMayNotBeUsed(): void
    {
        // The short open tag MAY NOT be used.
    }

    public function testTheShortEchoTagMayBeUsedInTemplates(): void
    {
        // The short echo tag MAY be used in templates.
    }

    public function testThereMayNotBeInlineHtml(): void
    {
        // There MAY NOT be any inline HTML in PHP code.
    }

    public function testDeprecatedPHPFunctionsMustBeAvoided(): void
    {
        // Deprecated PHP functions MUST be avoided.
    }

    public function testTheBacktickOperatorMayNotBeUsed(): void
    {
        // The backtick operator MAY NOT be used.
    }

    public function testTheGotoLanguageConstructMayNotBeUsed(): void
    {
        // The PHP `goto` language construct MAY NOT be used.
    }

    public function testTheGlobalKeywordMayNotBeUsed(): void
    {
        // The `global` keyword MAY NOT be used.
    }

    public function testThePHPSAPIConstantShouldBeUsed(): void
    {
        // The constant `PHP_SAPI` SHOULD be used instead of the
        // `php_sapi_name()` function.

        if (PHP_SAPI !== 'cli') {
            return;
        }
    }

    public function testAliasFunctionsShouldNotBeUsed(): void
    {
        // Alias functions SHOULD NOT be used.

        echo chop('abc ');
        echo sizeof([1, 2, 3]);
        echo is_null(456) ? 'y' : 'n';
        $foo = '1';
        settype($foo, 'int');
        var_dump($foo);
        $bar = [
            'foo' => 1,
            'bar' => 2,
            'baz' => 3,
        ];
        extract($bar);
        compact('foo', 'bar');
    }

    public function testSemicolonsUsage(): void
    {
        // There MAY NOT be a space before a semicolon. Redundant semicolons
        // SHOULD be avoided.

        $a     = $b{0};
        $hello = 1;
        $world = $hello; /* comment */
    }

    public function testNonExecutableCodeMMustBeRemoved(int $x): int
    {
        // Non executable code MUST be removed.

        return $x + 1;

        $x += 2; // Intentional dead code
    }

    public function testLanguageConstructSpacing(string $blah): string
    {
        // There MUST be a single space after language constructs.

        echo $blah;
        echo $blah;

        print $blah;
        print $blah;

        include $blah;
        include_once $blah;

        require $blah;
        require_once $blah;

        $obj = new MyClass();
        $obj = new MyClass();

        return $blah;
    }

    public function testLanguageConstructsMustBeCalledWithoutParentheses(): string
    {
        // Language constructs MUST be called without parentheses where possible.

        include 'file.php';
        include_once 'file.php';
        require 'file.php';
        require_once 'file.php';

        return 'foo';
    }

    public function testPHPFunctionCallsMustBeLowercase(string $text): string
    {
        // PHP function calls MUST be in lowercase.

        return strtolower($text);
    }
}

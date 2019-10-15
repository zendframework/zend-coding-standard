<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\fixed;

use Bar\Baz;
use DateTimeImmutable;
use DateTimeZone;

use function time;

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
}

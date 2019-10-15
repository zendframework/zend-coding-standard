<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\fixed;

use Bar\Baz;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;

use function strrev;
use function time;

use const DATE_RFC3339;
use Vendor\Package\{ClassA as A, ClassB, ClassC as C};
use Vendor\Package\SomeNamespace\ClassD as D;

use function Vendor\Package\{functionA, functionB, functionC};

use const Vendor\Package\{ConstantA, ConstantB, ConstantC};

class BasicCodingStandard extends Bar implements FooInterface
{
    public function sampleFunction(int $a, int $b = null): array
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

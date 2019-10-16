<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\fixed;

class BinaryOperators
{
    public function testBinaryOperators(): void
    {
        // All binary arithmetic, comparison, assignment, bitwise, logical,
        // string, and type operators MUST be preceded and followed by at least
        // one space.

        if ($a === $b) {
            $foo = $bar ?? $a ?? $b;
        } elseif ($a > $b) {
            $foo = $a + $b * $c;
        }
    }
}

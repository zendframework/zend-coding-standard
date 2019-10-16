<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\fixed;

class TernaryOperators
{
    public function testTernaryOperator(): void
    {
        // The conditional operator, also known simply as the ternary operator,
        // MUST be preceded and followed by at least one space around both the
        // `?` and `:` characters.

        $variable = $foo?'foo':'bar';

        // When the middle operand of the conditional operator is omitted, the
        // operator MUST follow the same style rules as other binary comparison
        // operators.

        $variable = $foo?:'bar';
    }
}

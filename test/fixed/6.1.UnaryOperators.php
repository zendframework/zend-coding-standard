<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\fixed;

class UnaryOperators
{
    public function testIncrementDecrementOperator(int $i, int $j): void
    {
        // The increment/decrement operators MUST NOT have any space between
        // the operator and operand.

        $i++;
        ++$j;
    }

    public function testCastingOperators(): int
    {
        // Type casting operators MUST NOT have any space within the
        // parentheses.
        //
        // There MUST be one whitespace after a type casting operator.

        return (int) '1';
    }

    public function testFormattingSpaceAfterNot(): bool
    {
        // There MUST be one whitespace after unary not.

        if (! true) {
            return false;
        }

        return true;
    }
}

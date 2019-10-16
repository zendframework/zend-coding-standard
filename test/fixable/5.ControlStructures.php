<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\fixed;

use InvalidArgumentException;
use Throwable;

class ControlStructures
{
    public function testControlStructures(): void
    {
        // The general style rules for control structures are as follows:
        //
        // There MUST be one space after the control structure keyword.
        //
        // There MUST NOT be a space after the opening parenthesis.
        //
        // There MUST NOT be a space before the closing parenthesis.
        //
        // There MUST be one space between the closing parenthesis and the
        // opening brace.
        //
        // The structure body MUST be indented once.
        //
        // The body MUST be on the next line after the opening brace.
        //
        // The closing brace MUST be on the next line after the body.
        //
        // The body of each structure MUST be enclosed by braces. This
        // standardizes how the structures look and reduces the likelihood of
        // introducing errors as new lines get added to the body.
    }

    public function testBreakAndContinueSpacing(): void
    {
        // There SHOULD be one single space after `break` and `continue`
        // structures with a numeric argument argument.

        for ($j = 0; $j < 10; ++$j) {
            if ($j === 0) {
                continue;
            }

            for ($i = 0; $i < 10; ++$i) {
                if ($i === 0) {
                    continue;
                }

                if ($i === 1) {
                    continue 2;
                }

                if ($i === 2) {
                    break;
                }

                if ($i === 3) {
                    break 2;
                }
            }
        }
    }

    public function testStatementsMayNotBeEmpty(string $foo): void
    {
        // Statements MAY NOT be empty, except for catch statements.

        switch ($foo) {
            case 'bar':
                break;
            default:
                break;
        }

        if ($foo) {
            // Just a comment
        } else {

        }

        try {
            throw new InvalidArgumentException('Error...');
        } catch (Throwable $e) {
            // Empty catch is allowed.
        }
    }
}

<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\fixed;

class Variables
{
    public function testVariablesMustBeInCamelCase(): void
    {
        // Variable names MUST be declared in camelCase.

        // Valid
        $camelCase    = true;
        $camel8number = true;

        // Invalid
        $_underscoreOnTheBeginning = false;
        $not_a_camel_case          = false;
    }
}

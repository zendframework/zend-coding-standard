<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\fixed;

use function is_int;

class KeywordsAndTypes
{
    public function testLowerCaseKeyword(string $string): void
    {
        // All PHP reserved keywords and types MUST be in lower case.

        return exit();
    }

    public function testLowerCaseConstant(string $var): bool
    {
        return $var === false || $var === null;
    }

    public function testLowerCaseType(string $var): int
    {
        return (int) $var;
    }

    public function testShortFormTypeKeywords(string $var): bool
    {
        // Short form of type keywords MUST be used i.e. bool instead of boolean,
        // int instead of integer etc.

        return is_int($var);
    }
}

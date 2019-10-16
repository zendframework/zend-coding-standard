<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\fixed;

use Iterator;

use function sprintf;

class ForEachStructure
{
    public function testForeachStructure(Iterator $iterable): void
    {
        foreach ($iterable as $key => $value) {
            echo sprintf('%d - %s', $key, $value);
        }
    }
}

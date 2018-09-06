<?php

declare(strict_types=1);

namespace ZendCodingStandardTest;

class PropertiesAndConstants
{
    /** @var string */
    public const FOO = 'bar';

    /** @var string */
    public $bar;

    /** @var bool */
    private $baz;

    public function __construct(bool $baz)
    {
        $this->baz = $baz;
    }

    public function bar() : bool
    {
        return $this->baz;
    }
}

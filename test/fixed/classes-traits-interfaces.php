<?php

declare(strict_types=1);

class Foo
{
    public $bar;
}

trait BarTrait
{
    public $var;
}

interface BazInterface
{
    public function big() : int;
}

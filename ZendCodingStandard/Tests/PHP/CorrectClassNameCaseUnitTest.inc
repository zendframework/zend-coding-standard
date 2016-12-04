<?php

namespace ZendCodingStandard\Tests\PHP;

use Arrayobject;
use Datetime;
use InvalidArgumentexception;
use Runtimeexception;
use Some\BarClass;
use Some\FooClass as Foo;
use Some\MyTrait;
use ZendCodingStandard\Tests\TestCase;

class MyClass
    extends Testcase
    implements
        \PHP_codesniffer_sniff,
        Arrayobject
{
    use \TestTrait;
    use Mytrait;

    public function method()
    {
        $dt = new Datetime();
        $ao = new arrayobject();
        $ai = new \Arrayiterator();
        $dr = \DATETIME
            ::
            createFromFormat('Y-m-d', '2015-12-16');
        $dx = \Datetime::createFromFormat('Y', '2016');

        $class = foo::class;

        $instance = new $class;
        $myConst = $class::CONSTANT;

        throw new Runtimeexception;

        new barclass();

        $dt = new
        \DatEtimE;
    }

    public function throwEx()
    {
        throw new Invalidargumentexception;
    }

    public function fromNamespace($foo)
    {
        $closure = function () use ($foo) {};

        return new CorrectclassnamecaseUnitTest();
    }
}

<?php
namespace MyApp\MyNamespace;

use MyApp\Traits;
use MyApp\Traits\Used1;
use MyApp\Unused1;
use MyApp\Service\Used2;
use MyApp\Plugin\UsedInDocComment1;
use OtherNamespace\UsedInDocComment2 as AliasUsedInComment2;
use UsedInDocComment2;
use Unused2;
use Used3 as AliasUsed3;
use MyApp\MyNamespace\Used4;
use MyApp\MyNamespace\Used5 as AliasUsed5;
use MyApp\Used6;
use Used7 as AliasUsed7;
use Bar\Baz\Used8;
use Bar\Baz\Used9 as AliasUsed9;
use Bar\Baz\Unused3;
use Bar\Baz\Unused4 as AliasUnused4;
use FooBar\Unused5;
use Used10;
use Used11;
use FooBar\Used12 as AliasUsed12;
use FooBar\Used13;
use Unused6, Unused7;
use Used14;
use FooBar\Used15;
use Used16 as AliasUsed16;

/**
 * @Used10
 * @Used11\Table
 * @AliasUsed12(...)
 */
class Foo
{
    use Traits\MyTrait;
    use Traits\Used1;

    protected function test1(Used1 $x, Used2 $y)
    {
    }

    /**
     * @return UsedInDocComment1
     */
    protected function test2()
    {
    }

    /**
     * @param UsedInDocComment2 $x
     */
    protected function test3($x)
    {
        /** @var AliasUsedInComment2 $y */
        $y = $x;
    }

    protected function test4(Used3 $x, Used4 $y)
    {
    }

    protected function test5(AliasUsed3 $x, AliasUsed5 $y)
    {
        new Used6();
        new AliasUsed7();
        Used8::create();
        AliasUsed9::create();

        // @var Unused5 $x
        $x = $y;

        /** @var Used13\MyType $x */
        $x = $y;
    }

    protected function test6() : Used14
    {
    }

    protected function test7() : Used15\MyClass
    {
    }

    protected function test8() : AliasUsed16
    {
    }
}

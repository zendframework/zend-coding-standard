<?php
namespace MyApp\MyNamespace;

use DateTime;

class Foo extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataprovider dataProvider1
     */
    public function test1(DateTime $x, $y)
    {
    }

    /**
     * Comment.
     * @dataProvider dataProvider2
     * @param string $x
     */
    public function test2($x)
    {
    }

    /**
     * @param string $x
     * @dataProvider dataProvider3
     */
    public function test3($x)
    {
    }

    /**
     * @dataProvider
     *
     * @dataProvider dataProvider5
     * @param string $x
     * @small
     */
    public function test4($x)
    {
    }

    /**
     * @small
     * @param string $x This is description
     *                  of the first param
     * @return void
     * @param int|null $y
     * @dataProvider dataProvider6
     * @dataProvider dataProvider7
     */
    public function test5($x, $y = null)
    {
    }

    /**
     * @dataProvider myProvider
     *
     * @param string $x
     */
    public function test6($x)
    {
    }

    /**
     * @dataProvider month
     */
    public function method()
    {
    }
}

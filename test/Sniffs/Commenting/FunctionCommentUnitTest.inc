<?php
namespace MyApp\MyNamespace;

use Bar;
use DateTime;

class Foo extends Bar
{
    /**
     * @inheritdoc
     */
    protected function test1(DateTime $x, $y)
    {
    }

    /**
     * Comment.
     *
     * {@inheritdoc}
     */
    protected function test2()
    {
    }
}

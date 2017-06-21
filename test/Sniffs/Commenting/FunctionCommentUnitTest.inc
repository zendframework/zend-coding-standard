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

    /**
     * @return array(integer => boolean)
     */
    private function returnsIntBoolArray()
    {
        return [1 => true];
    }

    /**
     * @return integer[]
     */
    private function returnsIntArray()
    {
        return [0, 1];
    }

    /**
     * @return boolean[]
     */
    private function returnsBoolArray()
    {
        return [true, false];
    }

    /**
     * @return array()
     */
    private function returnsArray()
    {
        return [];
    }
}

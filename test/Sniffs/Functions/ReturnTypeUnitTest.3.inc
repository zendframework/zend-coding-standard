<?php

namespace MyNamespace\Test\Functions;

class FunctionCommentReturn
{
    /**
     * @return mixed
     */
    public function test1($x)
    {
        return $x;
    }

    /**
     * @return mixed[]
     */
    public function test2($x)
    {
        return $x;
    }

    /**
     * @return mixed|mixed[]
     */
    public function test3($x)
    {
        return $x;
    }

    /**
     * @return false|mixed
     */
    public function test4($x)
    {
        return $x;
    }

    /**
     * @return bool|mixed
     */
    public function test5($x)
    {
        return $x;
    }

    /**
     * @return mixed
     */
    public function test6($x) : \Generator
    {
        return $x;
    }

    /**
     * @return mixed[]
     */
    public function test7($x) : \Generator
    {
        return $x;
    }

    /**
     * @return mixed
     */
    public function test8($x) : \Traversable
    {
        return $x;
    }

    /**
     * @return mixed[]
     */
    public function test9($x) : \Traversable
    {
        return $x;
    }

    /**
     * @return mixed
     */
    public function test10($x) : iterable
    {
        return $x;
    }

    /**
     * @return mixed[]
     */
    public function test11($x) : iterable
    {
        return $x;
    }

    /**
     * @return mixed
     */
    public function test12($x) : array
    {
        return $x;
    }

    /**
     * @return mixed[]
     */
    public function test13($x) : array
    {
        return $x;
    }

    /**
     * @return int
     */
    public function test14($x) : \Generator
    {
        return $x;
    }

    /**
     * @return int[]
     */
    public function test15($x) : \Generator
    {
        return $x;
    }

    /**
     * @return MyObj
     */
    public function test16($x) : \Generator
    {
        return $x;
    }

    /**
     * @return int
     */
    public function test17($x) : \Traversable
    {
        return $x;
    }

    /**
     * @return int[]
     */
    public function test18($x) : \Traversable
    {
        return $x;
    }

    /**
     * @return MyObj
     */
    public function test19($x) : \Traversable
    {
        return $x;
    }

    /**
     * @return int
     */
    public function test20($x) : iterable
    {
        return $x;
    }

    /**
     * @return int[]
     */
    public function test21($x) : iterable
    {
        return $x;
    }

    /**
     * @return MyObj
     */
    public function test22($x) : iterable
    {
        return $x;
    }

    /**
     * @return int
     */
    public function test23($x) : array
    {
        return $x;
    }

    /**
     * @return int[]
     */
    public function test24($x) : array
    {
        return $x;
    }

    /**
     * @return MyObj
     */
    public function test25($x) : array
    {
        return $x;
    }

    /**
     * @return null|\Generator
     */
    public function test26($x) : ?\Generator
    {
        return $x;
    }

    /**
     * @return array {
     *   @var bool $required Weather this element is required
     *   @var string $label The display name for this element
     * }
     */
    public function test27() : array
    {
        return [];
    }
}

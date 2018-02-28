<?php

namespace MyNamespace\Test\Functions;

class FunctionCommentReturn
{
    /**
     * @return mixed
     */
    public function test($x) : \Generator
    {
        return $x;
    }

    /**
     * @return mixed[]
     */
    public function test($x) : \Generator
    {
        return $x;
    }

    /**
     * @return mixed
     */
    public function test($x) : \Traversable
    {
        return $x;
    }

    /**
     * @return mixed[]
     */
    public function test($x) : \Traversable
    {
        return $x;
    }

    /**
     * @return mixed
     */
    public function test($x) : iterable
    {
        return $x;
    }

    /**
     * @return mixed[]
     */
    public function test($x) : iterable
    {
        return $x;
    }

    /**
     * @return mixed
     */
    public function test($x) : array
    {
        return $x;
    }

    /**
     * @return mixed[]
     */
    public function test($x) : array
    {
        return $x;
    }

    /**
     * @return int
     */
    public function test($x) : \Generator
    {
        return $x;
    }

    /**
     * @return int[]
     */
    public function test($x) : \Generator
    {
        return $x;
    }

    /**
     * @return MyObj
     */
    public function test($x) : \Generator
    {
        return $x;
    }

    /**
     * @return int
     */
    public function test($x) : \Traversable
    {
        return $x;
    }

    /**
     * @return int[]
     */
    public function test($x) : \Traversable
    {
        return $x;
    }

    /**
     * @return MyObj
     */
    public function test($x) : \Traversable
    {
        return $x;
    }

    /**
     * @return int
     */
    public function test($x) : iterable
    {
        return $x;
    }

    /**
     * @return int[]
     */
    public function test($x) : iterable
    {
        return $x;
    }

    /**
     * @return MyObj
     */
    public function test($x) : iterable
    {
        return $x;
    }

    /**
     * @return int
     */
    public function test($x) : array
    {
        return $x;
    }

    /**
     * @return int[]
     */
    public function test($x) : array
    {
        return $x;
    }

    /**
     * @return MyObj
     */
    public function test($x) : array
    {
        return $x;
    }

    /**
     * @return null|\Generator
     */
    public function test($x) : ?\Generator
    {
        return $x;
    }

    /**
     * @return array {
     *   @var bool $required Weather this element is required
     *   @var string $label The display name for this element
     * }
     */
    public function test() : array
    {
        return [];
    }
}

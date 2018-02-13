<?php

namespace MyNamespace\Test\Functions;

class FunctionParam
{
    /**
     * @param mixed $x
     */
    public function test1($x) {}

    /**
     * @param mixed[] $x
     */
    public function test2($x) {}

    /**
     * @param mixed|mixed[] $x
     */
    public function test3($x) {}

    /**
     * @param mixed $x
     */
    public function test4(int $x) {}

    /**
     * @param mixed $x
     */
    public function test5(MyObj $x) {}

    /**
     * @param mixed[] $x
     */
    public function test6(MyObj $x) {}

    /**
     * @param mixed $x
     */
    public function test7(\Generator $x) {}

    /**
     * @param mixed[] $x
     */
    public function test8(\Generator $x) {}

    /**
     * @param int $x
     */
    public function test9(\Generator $x) {}

    /**
     * @param MyObj $x
     */
    public function test10(\Generator $x) {}

    /**
     * @param mixed $x
     */
    public function test11(\Traversable $x) {}

    /**
     * @param mixed[] $x
     */
    public function test12(\Traversable $x) {}

    /**
     * @param int $x
     */
    public function test13(\Traversable $x) {}

    /**
     * @param MyObj $x
     */
    public function test14(\Traversable $x) {}

    /**
     * @param mixed $x
     */
    public function test15(iterable $x) {}

    /**
     * @param mixed[] $x
     */
    public function test16(iterable $x) {}

    /**
     * @param int $x
     */
    public function test17(iterable $x) {}

    /**
     * @param MyObj $x
     */
    public function test18(iterable $x) {}

    /**
     * @param mixed $x
     */
    public function test19(array $x) {}

    /**
     * @param mixed[] $x
     */
    public function test20(array $x) {}

    /**
     * @param int $x
     */
    public function test21(array $x) {}

    /**
     * @param MyObj $x
     */
    public function test22(array $x) {}

    /**
     * @param array|callable $a
     */
    public function paramArrayWithOtherTypes($a) {}

    /**
     * @param callable|iterable $a
     */
    public function paramIterableWithOtherTypes($a) {}

    /**
     * @param int|\Traversable $a
     */
    public function paramTraversableWithOtherTypes($a) {}

    /**
     * @param float|\Generator $a
     */
    public function paramGeneratorWithOtherTypes($a) {}
}

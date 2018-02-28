<?php

namespace MyNamespace\Test\Functions;

class FunctionParam
{
    /**
     * @param mixed $x
     */
    public function test(int $x) {}

    /**
     * @param mixed $x
     */
    public function test(MyObj $x) {}

    /**
     * @param mixed[] $x
     */
    public function test(MyObj $x) {}

    /**
     * @param mixed $x
     */
    public function test(\Generator $x) {}

    /**
     * @param mixed[] $x
     */
    public function test(\Generator $x) {}

    /**
     * @param int $x
     */
    public function test(\Generator $x) {}

    /**
     * @param MyObj $x
     */
    public function test(\Generator $x) {}

    /**
     * @param mixed $x
     */
    public function test(\Traversable $x) {}

    /**
     * @param mixed[] $x
     */
    public function test(\Traversable $x) {}

    /**
     * @param int $x
     */
    public function test(\Traversable $x) {}

    /**
     * @param MyObj $x
     */
    public function test(\Traversable $x) {}

    /**
     * @param mixed $x
     */
    public function test(iterable $x) {}

    /**
     * @param mixed[] $x
     */
    public function test(iterable $x) {}

    /**
     * @param int $x
     */
    public function test(iterable $x) {}

    /**
     * @param MyObj $x
     */
    public function test(iterable $x) {}

    /**
     * @param mixed $x
     */
    public function test(array $x) {}

    /**
     * @param mixed[] $x
     */
    public function test(array $x) {}

    /**
     * @param int $x
     */
    public function test(array $x) {}

    /**
     * @param MyObj $x
     */
    public function test(array $x) {}

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

    /**
     * @param
     */
    public function missingParamTypeAndName() {};

    /**
     * @param $a
     */
    public function missingParamType($a) {}

    /**
     * @param int
     */
    public function missingParamName($a) {}

    /**
     * @param int,string $a
     */
    public function invalidType($a);
}

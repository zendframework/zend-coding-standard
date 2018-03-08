<?php

namespace MyNamespace\Test;

use ArrayIterator as AI;
use DateTime;
use RuntimeException as Exception;
use ZTest\MyNamespace\ParentClass as ReturnTagParent;

class ReturnTag extends ReturnTagParent {
    /**
     * @return
     */
    public function test() {}

    /**
     * @return something
     */
    public function test() {}

    /**
     * @return $a
     */
    public function test() {}

    /**
     * @return $a something
     */
    public function test() {}

    /**
     * @return int $a
     */
    public function test() {}

    /**
     * @return mixed
     */
    public function test() {}

    /**
     * @return mixed[]
     */
    public function test() {}

    /**
     * @return mixed|mixed[]
     */
    public function test() {}

    /**
     * @return string|\DateTime[]|\Traversable
     */
    public function test() {}

    /**
     * @return ReturnTag
     */
    public function test() {}

    /**
     * @return \MyNamespace\Test\ReturnTag
     */
    public function test() {}

    /**
     * @return ReturnTagParent
     */
    public function test() {}

    /**
     * @return \ZTest\MyNamespace\ParentClass
     */
    public function test() {}

    /**
     * @return ReturnTag[]
     */
    public function test() {}

    /**
     * @return \MyNamespace\Test\ReturnTag[]
     */
    public function test() {}

    /**
     * @return ReturnTagParent[]
     */
    public function test() {}

    /**
     * @return \ZTest\MyNamespace\ParentClass[]
     */
    public function test() {}

    /**
     * @return ParentClass
     */
    public function test() {}

    /**
     * @return ParentClass[]
     */
    public function test() {}

    /**
     * @return bool|int|bool
     */
    public function test() {}

    /**
     * @return void
     */
    public function test() {}

    /**
     * @return Void
     */
    public function test() {}

    /**
     * @return void Description.
     */
    public function test() {}

    /**
     * @return void[]
     */
    public function test() {}

    /**
     * @return true|false
     */
    public function test() {}

    /**
     * @return bool|true
     */
    public function test() {}

    /**
     * @return bool|false
     */
    public function test() {}

    /**
     * @return \DateTime
     */
    public function test() {}

    /**
     * @return ?array
     */
    public function test() {}

    /**
     * @return |int|
     */
    public function test() {}

    /**
     * @return $a
     */
    public function test() {}

    /**
     * @return String
     */
    public function test() {}

    /**
     * @return string|int|float
     */
    public function test() {}

    /**
     * @return DateTime|int|bool
     */
    public function test() {}

    /**
     * @return \ArrayObject|\ArrayAccess|DateTime|null
     */
    public function test() {}

    /**
     * @return boolean
     */
    public function test() {}

    /**
     * @return null[]|string[]
     */
    public function test() {}

    /**
     * @return $this
     */
    public function test() {}

    /**
     * @return $This
     */
    public function test() {}

    /**
     * @return $this|null
     */
    public function test() {}

    /**
     * @return $THis|null
     */
    public function test() {}

    /**
     * @return null|$this
     */
    public function test() {}

    /**
     * @return null|$ThIs
     */
    public function test() {}

    /**
     * @return int|$this
     */
    public function test() {}

    /**
     * @return null
     */
    public function test() {}

    /**
     * @return null[]
     */
    public function test() {}

    /**
     * @return null[][]
     */
    public function test() {}

    /**
     * @return null|null[]
     */
    public function test() {}

    /**
     * @return mixed|string
     */
    public function test() {}

    /**
     * @return array|mixed[]
     */
    public function test() {}

    /**
     * @return int|void
     */
    public function test() {}

    /**
     * @return ai
     */
    public function test() {}

    /**
     * @return AI
     */
    public function test() {}

    /**
     * @return \ArrayIterator
     */
    public function test() {}

    /**
     * @return int,string
     */
    abstract public function invalidTypeNotation();

    /**
     * @return array()
     */
    abstract public function invalidTypeNotationArray();

    /**
     * @return array[string]
     */
    abstract public function invalidTypeNotationArrayKeys();

    /**
     * @return Array[]|iterable|array|ITerable[][]|Traversable|\traversable[]
     */
    public function test() {}

    /**
     * @return Null|TRUE
     */
    public function test() {}

    /**
     * @return null|False
     */
    public function test() {}

    /**
     * @return self
     */
    public function test() {}

    /**
     * @return static
     */
    public function test() {}

    /**
     * @return parent
     */
    public function test() {}

    /**
     * @return Self
     */
    public function test() {}

    /**
     * @return Static
     */
    public function test() {}

    /**
     * @return Parent
     */
    public function test() {}

    /**
     * @return array {
     *     @var bool $required
     *     @var string $label
     *     @var array $arr {
     *         @var int $counter
     *     }
     * }
     */
    public function test() {}
}

interface MyInterface extends MyOne, MyTwo
{
    /**
     * @return MyInterface
     */
    public function method1($self);

    /**
     * @return MyOne
     */
    public function method2($myOne);

    /**
     * @return MyTwo
     */
    public function method3($myTwo);
}

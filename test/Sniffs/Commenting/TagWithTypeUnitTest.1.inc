<?php

namespace MyNamespace\Test;

use ArrayIterator as AI;
use DateTime;
use RuntimeException as Exception;
use ZTest\MyNamespace\ParentClass as ParamTagParent;

class ParamTag extends ParamTagParent {
    /**
     * @param
     */
    public function test($a) {}

    /**
     * @param something
     */
    public function test($a) {}

    /**
     * @param $a something
     */
    public function test($a) {}

    /**
     * @param ...$a
     */
    public function test(...$a) {}

    /**
     * @param $c
     */
    public function test($a) {}

    /**
     * @param mixed $a
     */
    public function test($a) {}

    /**
     * @param mixed[] $a
     */
    public function test($a) {}

    /**
     * @param mixed|mixed[] $a
     */
    public function test($a) {}

    /**
     * @param string|\DateTime[]|\Traversable $a
     */
    public function test($a) {}

    /**
     * @param ParamTag $a
     */
    public function test($a) {}

    /**
     * @param \MyNamespace\Test\ParamTag $a
     */
    public function test($a) {}

    /**
     * @param ParamTagParent $a
     */
    public function test($a) {}

    /**
     * @param \ZTest\MyNamespace\ParentClass $a
     */
    public function test($a) {}

    /**
     * @param ParamTag[] $a
     */
    public function test($a) {}

    /**
     * @param \MyNamespace\Test\ParamTag[] $a
     */
    public function test($a) {}

    /**
     * @param ParamTagParent[] $a
     */
    public function test($a) {}

    /**
     * @param \ZTest\MyNamespace\ParentClass[] $a
     */
    public function test($a) {}

    /**
     * @param ParentClass $a
     */
    public function test($a) {}

    /**
     * @param ParentClass[] $a
     */
    public function test($a) {}

    /**
     * @param bool|int|bool $a
     */
    public function test($a) {}

    /**
     * @param void $a
     */
    public function test($a) {}

    /**
     * @param void[] $a
     */
    public function test($a) {}

    /**
     * @param true|false $a
     */
    public function test($a) {}

    /**
     * @param bool|true $a
     */
    public function test($a) {}

    /**
     * @param bool|false $a
     */
    public function test($a) {}

    /**
     * @param \DateTime $a
     */
    public function test($a) {}

    /**
     * @param ?array $a
     */
    public function test($a) {}

    /**
     * @param |int| $a
     */
    public function test($a) {}

    /**
     * @param $a
     */
    public function test($a) {}

    /**
     * @param \RuntimeException $a
     */
    public function test(\Throwable $a) {}

    /**
     * @param String $a
     */
    public function test($a) {}

    /**
     * @param string|int|float $a
     */
    public function test($a) {}

    /**
     * @param DateTime|int|bool $a
     */
    public function test($a) {}

    /**
     * @param \ArrayObject|\ArrayAccess|DateTime|null $a
     */
    public function test($a) {}

    /**
     * @param boolean $a
     */
    public function test($a) {}

    /**
     * @param integer $a
     */
    public function test($a) {}

    /**
     * @param double $a
     */
    public function test($a) {}

    /**
     * @param null[]|string[] $a
     */
    public function test($a) {}

    /**
     * @param $this $a
     */
    public function test($a) {}

    /**
     * @param null $a
     */
    public function test($a) {}

    /**
     * @param null[] $a
     */
    public function test($a) {}

    /**
     * @param null[][] $a
     */
    public function test($a) {}

    /**
     * @param null|null[] $a
     */
    public function test($a) {}

    /**
     * @param mixed|string $a
     */
    public function test($a) {}

    /**
     * @param array|mixed[] $a
     */
    public function test($a) {}

    /**
     * @param int|void $a
     */
    public function test($a) {}

    /**
     * @param ai $a
     */
    public function test($a) {}

    /**
     * @param AI $a
     */
    public function test($a) {}

    /**
     * @param \ArrayIterator $a
     */
    public function test($a) {}

    /**
     * @param self $a
     */
    public function test($a) {}

    /**
     * @param static $a
     */
    public function test($a) {}

    /**
     * @param parent $a
     */
    public function test($a) {}

    /**
     * @param Self $a
     */
    public function test($a) {}

    /**
     * @param Static $a
     */
    public function test($a) {}

    /**
     * @param Parent $a
     */
    public function test($a) {}

    /**
     * @param array $a {
     *     @var bool $required
     *     @var string $label
     *     @var array $arr {
     *         @var int $counter
     *     }
     * }
     * @param array $b {
     *     @var int $int
     * }
     */
    public function test($a, $b) {}
}

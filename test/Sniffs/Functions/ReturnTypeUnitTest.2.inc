<?php

namespace MyNamespace\Test\Functions;

abstract class FunctionCommentReturn
{
    /**
     * @return array|int|\ArrayObject[]|\DateTime|\DateTime[]
     */
    abstract public function arrayTypeSpecification() : array;

    /**
     * @return null|string|\DateTime[]
     */
    abstract public function nullArrayTypeSpecification() : ?array;

    /**
     * @return iterable|string|\DateTime[]|MyIterableClass
     */
    abstract public function iterableTypeSpecification() : iterable;

    /**
     * @return null|bool|AnotherClass|\DateTime[]|MyIterableClass[]
     */
    abstract public function nullIterableTypeSpecification() : ?iterable;

    /**
     * @return int|\ArrayObject[]|\DateTime|\DateTime[]|\Traversable
     */
    abstract public function traversableTypeSpecification() : Traversable;

    /**
     * @return null|bool|object|\DateTime[]
     */
    abstract public function nullTraversableTypeSpecification() : ?\Traversable;

    /**
     * @return $this
     */
    public function returnNotThis($a)
    {
        return array();
        return (array) $a;
        return (bool) $a;
        return ! $a;
        return function () {};
        return 'string';
        return __DIR__;
        return (float) $a;
        return "val $a";
        return 1.2;
        return false;
        return __FILE__;
        return (int) $a;
        return 1;
        return new static();
        return \MyClass::class;
        return ($a + 1);
        return [];
        return (object) $a;
        return self::CONST;
        return static::method();
        return (string) $a;
        return parent::method();
        return true;
        return $a;
        return $this;
    }

    public function noVoidAndNoReturnStatement() : string
    {
    }

    public function noVoidAndHasVoidReturnStatement($a) : int
    {
        if ($a) {
            return;
        }

        return 1;
    }

    /**
     * @return string
     */
    public function noVoidAndNoReturnStatementTag()
    {
    }

    /**
     * @return string
     */
    public function noVoidAndHasVoidReturnStatementTag($a)
    {
        if ($a) {
            return;
        }

        return 'string';
    }

    public function voidAndHasReturnStatement($a) : void
    {
        return $a;
    }

    /**
     * @return void
     */
    public function voidAndHasReturnStatementTag($a)
    {
        return $a;
    }

    /**
     * @return null|array|bool|int|string[]|\DateTime|\DateTime[]
     */
    public function returnArrayOnlyHasNonArrayTypes1()
    {
        return [];
    }

    /**
     * @return null|float|iterable
     */
    public function returnArrayOnlyHasNonArrayTypes2()
    {
        return array();
    }

    /**
     * @return object|string|\ArrayObject[]
     */
    public function returnArrayOnlyHasNonArrayTypes3($a)
    {
        return (array) $a;
    }

    public function returnArrayOnlyHasArrayReturnType($a) : array
    {
        if ($a) {
            return [$a];
        }

        return array();
    }

    public function returnArrayOrNullHasNullableArrayReturnType($a) : ?array
    {
        if ($a) {
            return (array) $a;
        }

        return null;
    }

    public function returnArrayOnlyHasIterableReturnType($a) : iterable
    {
        if ($a) {
            return [$a];
        }

        return array();
    }

    public function returnArrayOrNullHasNullableIterableReturnType($a) : ?iterable
    {
        if ($a) {
            return (array) $a;
        }

        return null;
    }

    /**
     * @return null|bool
     */
    public function returnBoolOnlyHasNonBooleanType1($a)
    {
        return (bool) $a;
    }

    /**
     * @return int|string
     */
    public function returnBoolOnlyHasNonBooleanType2($a)
    {
        return ! $a;
    }

    /**
     * @return float|\DateTime
     */
    public function returnBoolOnlyHasNonBooleanType3($a)
    {
        return ! $this->method($a) && $a[0];
    }

    /**
     * @return array|\DateTime[]
     */
    public function returnBoolOnlyHasNonBooleanType4()
    {
        return ! ['array'];
    }

    /**
     * @return int
     */
    public function returnBoolOnlyHasNonBooleanType5($a)
    {
        if ($a) {
            return true;
        }

        return false;
    }

    /**
     * @return int|string
     */
    public function returnPossiblyNotBoolAndDoesNotHaveBoolType($a)
    {
        return ! $a ? true : null;
    }

    /**
     * @return bool
     */
    public function returnBoolAndHasBooleanType1($a)
    {
        return ! $a;
    }

    /**
     * @return boolean
     */
    public function returnBoolAndHasBooleanType2($a)
    {
        return (bool) $a;
    }

    public function returnBoolAndHasBooleanReturnType1($a) : bool
    {
        return ! $a;
    }

    public function returnBoolAndHasBooleanReturnType2($a) : ?bool
    {
        return (bool) $a;
    }

    public function returnBoolAndHasBooleanReturnType3($a) : bool
    {
        if ($a) {
            return true;
        }

        return false;
    }

    public function returnBooleanAndHasNotBooleanReturnType($a) : int
    {
        if ($a) {
            return true;
        }

        return 1;
    }

    /**
     * @return false
     */
    public function returnTrueOnlyHasNotTrueValue()
    {
        return true;
    }

    /**
     * @return true
     */
    public function returnFalseOnlyHasNotFalseValue()
    {
        return false;
    }

    /**
     * @return string
     */
    public function returnIntAndHasNoIntType1($a)
    {
        if ($a) {
            return 2 * $a;
        }

        return 0;
    }

    /**
     * @return float
     */
    public function returnIntAndHasNoIntType2($a)
    {
        if (! is_float($a)) {
            return 1;
        }

        return $a;
    }

    public function returnIntAndHasNoReturnTypeInt1() : string
    {
        return 1;
    }

    public function returnIntAndHasNoReturnTypeInt2() : float
    {
        return 1;
    }

    public function returnIntAndHasIntReturnType1() : int
    {
        return 1;
    }

    public function returnIntAndHasIntReturnType2() : ?int
    {
        return 0;
    }

    public function returnPossiblyNotIntAndHasIntReturnType($a) : ?int
    {
        return 5 * $a;
    }

    /**
     * @return float
     */
    public function returnPossiblyNotIntAndDoesNotHaveIntType($a)
    {
        return 5 * $a;
    }

    /**
     * @return float
     */
    public function returnNullButDoesNotHaveNullType($a)
    {
        if ($a) {
            return $a;
        }

        return null;
    }

    public function returnNullButReturnTypeIsNotNullable($a) : int
    {
        if ($a) {
            return $a;
        }

        return null;
    }

    /**
     * @return string
     */
    public function returnFloatAndHasNoFloatType1($a)
    {
        if ($a) {
            return 2 * $a;
        }

        return 0.0;
    }

    /**
     * @return int
     */
    public function returnFloatAndHasNoFloatType2($a)
    {
        if (! is_int($a)) {
            return 1.0;
        }

        return $a;
    }

    public function returnFloatAndHasNoReturnTypeFloat1() : string
    {
        return 1.0;
    }

    public function returnFloatAndHasNoReturnTypeFloat2() : int
    {
        return 1.0;
    }

    public function returnFloatAndHasFloatReturnType1() : float
    {
        return 1.0;
    }

    public function returnFloatAndHasFloatReturnType2() : ?float
    {
        return 0.0;
    }

    public function returnFloatExpressionAndDoesNotHaveFloatReturnType($a) : ?int
    {
        return 5.0 * $a;
    }

    /**
     * @return int
     */
    public function returnFloatExpressionAndDoesNotHaveFloatType($a)
    {
        return 5.0 * $a;
    }

    /**
     * @return object
     */
    public function returnNewInstanceFromVariable($a)
    {
        return new $a;
    }

    /**
     * @return OtherClass
     */
    public function returnNewInstance()
    {
        return new MyClass;
    }

    /**
     * @return MyInterface2
     */
    public function returnTwoDifferentInstances($a) : MyNamespace\MyInterface1
    {
        if ($a) {
            return new MyNamespace\ClassA();
        }

        return new MyNamespace\ClassX();
    }

    /**
     * @return self
     */
    public function returnThisOnlyAndHasSelfInTag()
    {
        return $this;
    }

    /**
     * @return FunctionCommentReturn
     */
    public function returnThisOnlyAndHasClassNameInTag()
    {
        return $this;
    }

    /**
     * @return \MyNamespace\Test\Functions\FunctionCommentReturn
     */
    public function returnThisOnlyAndHasFCQNClassNameInTag()
    {
        return $this;
    }
}
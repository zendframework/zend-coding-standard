<?php

namespace MyNamespace\Test\Functions;

abstract class FunctionCommentReturn
{
    /**
     * @return int
     */
    public function __construct() : int
    {
        return 1;
    }

    /**
     * @return bool
     */
    public function __destruct() : bool
    {
        return false;
    }

    abstract public function voidAbstractMethodNoReturnTag();

    /**
     * @return
     */
    abstract public function noTypeInReturnTag();

    /**
     * @return int
     * @return string
     */
    abstract public function duplicatedTag();

    /**
     * @param mixed $a
     */
    public function missingReturnTag($a)
    {
        return $a;
    }

    public function missingReturnTagYield(\DateTimeInterface $a)
    {
        yield $a;
    }

    public function missingReturnTagYieldFrom(\DateTime $a)
    {
        yield from $this->missingReturnTypeYield($a);
    }

    public function returnType() : int
    {
        return 1;
    }

    public function returnTypeYield() : int
    {
        yield 1;
    }

    public function returnTypeYieldFrom() : int
    {
        yield from $this->returnTypeYieldFrom();
    }

    public function withClosure()
    {
        $a = function () {
            return 1;
        };
    }

    public function withClosureYield()
    {
        $a = function () {
            yield 1;
        };
    }

    public function withClosureYieldFrom()
    {
        $a = function () {
            yield from $this->returnTypeYield();
        };
    }

    public function returnTypeWithClosure()
    {
        $a = function (&$x) {
            if ($x) {
                return;
            }

            ++$x;
        };

        return $a;
    }

    /**
     * @return int
     */
    public function invalidReturnTagWithClosure()
    {
        $a = function () {
            return 1;
        };
    }

    public function invalidReturnTypeWithClosure() : int
    {
        $a = function () {
            return 1;
        };
    }

    public function withAnonClass()
    {
        $a = new class {
            public function a() {
                return 1;
            }
        };
    }

    public function withAnonClassYield()
    {
        $a = new class {
            public function a() {
                yield 1;
            }
        };
    }

    public function withAnonClassYieldFrom()
    {
        $a = new class {
            public function a() {
                yield from b();
            }
        };
    }

    public function withAnonClassReturnType()
    {
        $a = new class {
            public function a() {
                return;
            }
        };

        return 1;
    }

    /**
     * @return	int	Description.
     */
    public function tabSeparatedDoc()
    {
        return 1;
    }

    /**
     * @return    int   Description.
     */
    public function moreSpacesInDoc()
    {
        return 1;
    }

    /**
     * @return void Description is not allowed.
     */
    public function voidWithDescription() {}

    /**
     * @return void
     */
    public function redundantReturnVoidTag() {}

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
     * @return $This
     */
    abstract public function returnThisInvalidCase1();

    /**
     * @return null|$THIS
     */
    abstract public function returnThisInvalidCase2();

    /**
     * @return null
     */
    abstract public function returnOnlyNull();

    /**
     * @return null[][]
     */
    abstract public function returnOnlyNullArray();

    /**
     * @return void|int
     */
    abstract public function voidMixedWithOtherTypes();

    /**
     * @return mixed
     */
    abstract public function mixedTypeIsNotAllowed();

    /**
     * @return mixed[][]
     */
    abstract public function mixedArrayTypeIsNotAllowed();

    abstract public function returnTypeArrayNeedsSpecification() : array;

    abstract public function returnTypeNullableArrayNeedsSpecification() : ?array;

    abstract public function returnTypeTraversableNeedsSpecification() : \Traversable;

    abstract public function returnTypeNullableTraversableNeedsSpecificaiton() : ?\Traversable;

    abstract public function returnTypeTraversableWithoutNSNeedsSpecification() : Traversable;

    abstract public function returnTypeNullableTraversableWithoutNSNeedsSpecification() : ?Traversable;

    abstract public function returnTypeIterableNeedsSpecification() : iterable;

    abstract public function returnTypeNullableIterableNeedsSpecification() : ?iterable;

    /**
     * @return array
     */
    abstract public function tagReturnTypeArrayNeedsSpecification();

    /**
     * @return array[]
     */
    abstract public function tarReturnTypeArrayOfArraysNeedsSpecification();

    /**
     * @return \Traversable
     */
    abstract public function tagReturnTypeTraversableNeedsSpecification();

    /**
     * @return Traversable[]
     */
    abstract public function tagReturnTypeArrayOfTraversableNeedsSpecification();

    /**
     * @return iterable
     */
    abstract public function tagReturnTypeIterableNeedsSpecification();

    /**
     * @return iterable[]
     */
    abstract public function tagReturnTypeArrayOfIterableNeedsSpecification();

    /**
     * @return Array[]|iterable|array|ITerable[][]|Traversable|\traversable[]
     */
    abstract public function allTagTypesNeedSpecification();

    /**
     * @return Null|TRUE
     */
    public function returnNullOrTrueWrongCaseInTag()
    {
        return mt_rand(0, 1) ? true : null;
    }

    /**
     * @return null|False
     */
    public function returnNullOrFalseWrongCaseInTag()
    {
        return mt_rand(0, 1) ? null : false;
    }
}

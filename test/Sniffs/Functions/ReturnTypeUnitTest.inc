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

    abstract public function returnTypeArrayDoesNotNeedSpecification() : array;

    abstract public function returnTypeNullableArrayDoesNotNeedSpecification() : ?array;

    abstract public function returnTypeTraversableDoesNotNeedSpecification() : \Traversable;

    abstract public function returnTypeNullableTraversableDoesNotNeedSpecificaiton() : ?\Traversable;

    abstract public function returnTypeTraversableWithoutNSDoesNotNeedSpecification() : Traversable;

    abstract public function returnTypeNullableTraversableWithoutNSDoesNotNeedSpecification() : ?Traversable;

    abstract public function returnTypeGeneratorDoesNotNeedSpecification() : \Generator;

    abstract public function returnTypeNullableGeneratorDoesNotNeedSpecificaiton() : ?\Generator;

    abstract public function returnTypeGeneratorWithoutNSDoesNotNeedSpecification() : Generator;

    abstract public function returnTypeNullableGeneratorWithoutNSDoesNotNeedSpecification() : ?Generator;

    abstract public function returnTypeIterableDoesNotNeedSpecification() : iterable;

    abstract public function returnTypeNullableIterableDoesNotNeedSpecification() : ?iterable;

    /**
     * @return bool|int We don't know the exact type here.
     */
    public function returnNullOrTrueWrongCaseInTag()
    {
        return mt_rand(0, 1) ? true : null;
    }

    /**
     * @return string We don't know the exact type here.
     */
    public function returnNullOrFalseWrongCaseInTag()
    {
        return mt_rand(0, 1) ? null : false;
    }

    /**
     * @return array|callable|iterable|\Generator|\Traversable
     */
    public function returnArrayOrSomethingElse($x)
    {
        if ($x) {
            return function() {
            };
        }

        return [];
    }

    /**
     * @return
     */
    abstract public function missingType();

    /**
     * @return int,string
     */
    abstract public function invalidType();

    /**
     * @return null
     */
    abstract public function returnNull();

    /**
     * @return null[]
     */
    abstract public function returnNullArray();
}

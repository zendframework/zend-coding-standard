<?php

declare(strict_types=1);

class Anything
{
}

class Whatever extends Anything
{
    public function magicConstant() : string
    {
        return self::class;
    }

    public function getClassWithoutArguments() : string
    {
        return self::class;
    }

    public function getClassWithThis() : string
    {
        return static::class;
    }

    public function getParentClass() : string
    {
        return parent::class;
    }

    public function getCalledClass() : string
    {
        return static::class;
    }

    public function getMethodWithFullyQualifiedName() : string
    {
        return static::class;
    }

    public function classNotationInsteadOfString() : string
    {
        $a = PHP_CodeSniffer\Config::class;

        return ArrayObject::class;
    }
}

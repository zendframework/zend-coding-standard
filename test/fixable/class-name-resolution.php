<?php

declare(strict_types=1);

class Anything
{
}

class Whatever extends Anything
{
    public function magicConstant() : string
    {
        return __CLASS__;
    }

    public function getClassWithoutArguments() : string
    {
        return get_class();
    }

    public function getClassWithThis() : string
    {
        return get_class($this);
    }

    public function getParentClass() : string
    {
        return get_parent_class();
    }

    public function getCalledClass() : string
    {
        return get_called_class();
    }

    public function getMethodWithFullyQualifiedName() : string
    {
        return \get_called_class();
    }

    public function classNotationInsteadOfString() : string
    {
        $a = '\PHP_CodeSniffer\Config';

        return '\ArrayObject';
    }
}

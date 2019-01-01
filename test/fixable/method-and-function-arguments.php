<?php

declare(strict_types=1);

namespace MethodsAndFunctionArguments;

class ClassName
{
    public function foo(int $arg1, &$arg2, $arg3 = []):void
    {
    }

    public function aVeryLongMethodName(
        ClassTypeHint $arg1,
        &$arg2,
        array $arg3 = []
    )  :  array {
        return [];
    }

    public function functionName(int $arg1, $arg2): string
    {
        return'foo';
    }

    public function anotherFunction(
        string $foo,
        string $bar,
        int $baz
    ) : string {
        return 'foo';
    }

    public function returnTypeVariations(string $arg1 = null, string $arg2 = null):?string
    {
        return $arg1 ?? $arg2;
    }

    public function splat(... $args)
    {
        return $args ?? [];
    }

    public function reference(& $ref, array $arr)
    {
        $obj =& new Foo();
        $bar =& $arr;

        return $obj->process($bar);
    }
}

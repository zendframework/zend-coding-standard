<?php
namespace MyApp\MyNamespace;

class MyClass
{
    const C0 = 0;

    public const C1 = 1;
    protected const C2 = 2;
    private const C3 = 3;
}

interface MyInterface
{
    const C0 = 0;

    public const C1 = 1;
    protected const C2 = 2;
    private const C3 = 3;
}

new class () {
    const C0 = 0;

    public const C1 = 1;
    protected const C2 = 2;
    private const C3 = 3;
};

const C0 = 0;

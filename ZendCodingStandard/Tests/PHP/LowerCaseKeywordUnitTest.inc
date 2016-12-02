<?php

NAMESPACE MyNamespace;

USE Foo\Bar;

ABSTRACT CLASS MyClass
    EXTENDS MyParentClass
    IMPLEMENTS MyInterface
{
    USE MyTrait;

    PUBLIC STATIC FUNCTION method()
    {
        PARENT::method();
        NEW \DateTime();

        STATIC::method();
        SELF::method();

        IF (ISSET($var) OR FALSE) {
        } ELSEIF (EMPTY($var) AND TRUE XOR NULL) {
        } ELSE {
        }
    }

    ABSTRACT PROTECTED FUNCTION protectedMethod();

    PRIVATE FUNCTION privateMethod($a, $b, CALLABLE $c)
    {
        WHILE ($a) {
        }

        DO {
        } WHILE ($b);

        FOR ($i = $c; $i < 1; $i++) {
            CONTINUE;
        }

        TRY {
            INCLUDE 'file';
            INCLUDE_ONCE 'file2';
            REQUIRE 'file3';
            REQUIRE_ONCE 'file4';
        } CATCH (\Exception $ex) {
            THROW NEW \RuntimeException();
        }

        PRINT 'Hello';
        ECHO $b INSTANCEOF \Exception ? 'Hello' : 'World';

        $x = CLONE $a;
        YIELD $a => $b;
    }

    FINAL PUBLIC FUNCTION finalMethod()
    {
        SWITCH (1) {
            CASE 0:
                RETURN FUNCTION () {};
            DEFAULT:
                BREAK;
        }
    }
}

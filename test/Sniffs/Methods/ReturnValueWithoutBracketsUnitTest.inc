<?php

abstract class ReturnValueWithoutBrackets
{
    public function method1()
    {
        return('hello');
    }

    public function method2($x, $y, $z)
    {
        return (($x || $y) && ($z));
    }

    public function method3($x, $y, $z)
    {
        return ($x && $y) || ($z);
    }

    public function method4()
    {
        return (function() {
            return ('foo');
        });
    }

    public function method5()
    {
        return (function() {
            return('bar');
        })();
    }

    public function method6()
    {
        return (1 + 2);
    }

    private function method7()
    {
        return (1 + 2) * 3;
    }
}

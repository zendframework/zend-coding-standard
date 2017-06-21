<?php

use CoNsT E_ALL as E;
use Function array_merge as am;
use ZendCodingStandard\CodingStandard;
use CONST   E_ERROR as EE;
use funcTION\array_diff as ad;

class MyClass {
    use MyTrait;

    public function method() {
        $c = new class() {
            use AnotherTrait;
        };
    }
}

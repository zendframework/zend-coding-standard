<?php

$a = SEEK_END;

if ($a === E_WARNING) {
    $a = E_ALL | E_NOTICE;
}

$obj = new \DateTime();
$x = $obj::E_PARSE;
$y = $obj->E_ERROR;
$z = $obj::ATOM;

function SEEK_CUR() {
}

SEEK_CUR();

$b = &T_WHITESPACE;

define('MY_CONST', 'value');
$myConst = MY_CONST;

$c = \T_VAR;

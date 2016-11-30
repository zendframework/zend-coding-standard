<?php

$globals = [
    $_SERVER,
    $_GET,
    $_POST,
    $_REQUEST,
    $_SESSION,
    $_ENV,
    $_COOKIE,
    $_FILES,
    $GLOBALS,
];

$_underscoreOnTheBeginning = false;
$not_a_camel_case = false;
$camelCase = true;
$camel8number = true;

echo \Library::$_variable;
echo \Library::$_another_variable;

class Foo {
    protected $_this_is_not_handled_by_this_sniff;
}

$string = "This $_some_variable is not {$handled_by} this sniff.";
$string .= $_some_variable;
$string .= $camelCase;

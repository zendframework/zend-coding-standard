<?php

declare(strict_types=1);

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
$not_a_camel_case          = false;
$camelCase                 = true;
$camel8number              = true;

echo Library::$_variable;
echo Library::$_another_variable;

class VariableNames
{
    protected $_this_is_not_handled_by_this_sniff;
}

$string  = $_some_variable;
$string .= $camelCase;

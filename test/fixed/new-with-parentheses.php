<?php

declare(strict_types=1);

$foo = new DateTimeImmutable();

$barClassName = 'Bar';
$bar          = new $barClassName();

$classNamesInArray = ['Baz'];
$foo               = new $classNamesInArray[0]();

$classNamesInObject      = new stdClass();
$classNamesInObject->foo = 'Foo';
$foo                     = new $classNamesInObject->foo();

$whitespaceBetweenClassNameAndParentheses = new stdClass();

$x = [
    new stdClass(),
];

$y = [new stdClass()];

$z = new stdClass() ? new stdClass() : new stdClass();

$q = $q ?: new stdClass();
$e = $e ?? new stdClass();

// The parentheses around `(new Response())` should not be removed
// https://github.com/slevomat/coding-standard/issues/478
$response = (new Response())
    ->withStatus(200)
    ->withAddedHeader('Content-Type', 'text/plain');

$anonymousClass = new class() extends DateTime {
};

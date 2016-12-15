<?php

new DateTime;
new Exception;

new InvalidArgumentException(
    (new \DateTime)->format('Y-m-d')
);

class MyClass {
    public function __construct(DateTime $dt = null, $param = '') {}

    static public function instance() {
        return new self;
    }

    static public function staticInstance() {
        return new static;
    }
}

new MyClass;
new MyClass(new DateTime);
new MyClass(new DateTime, $_GET);

$var = DateTime::class;
new $var;

[new DateTime];
array(new DateTime);

$anonymousClass = new class{};
$anonymousClass = new class {};

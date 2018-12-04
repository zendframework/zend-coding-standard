<?php

declare(strict_types=1);

$instance = new class() {
};

// Brace on the same line
$instance = new class() extends Foo implements HandleableInterface {
    // Class content
};

// Brace on the next line
$instance = new class() extends Foo implements
    ArrayAccess,
    Countable,
    Serializable
{
    // Class content
};

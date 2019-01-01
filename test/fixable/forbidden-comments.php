<?php

declare(strict_types=1);

/** Created by PhpStorm. */

namespace Test;

class ForbiddenComments
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        echo 'Hello';
    }

    /**
     * Bar getter.
     */
    public function getBar() : int
    {
        return 123;
    }

    /**
     * Very important getter.
     */
    public function getBaz() : int
    {
        return 456;
    } // end getBaz
} // end class

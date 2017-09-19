<?php

class NoInlineCommentAfterCurlyCloseUnitTest
{
    public function __construct($a)
    {
        while ($a > 0) {
            if ($a) {
                --$a;
            } // end if
        } // end while
    } // end __construct

    public function method($a)
    {
        switch ($a) {
            case 1:
                if ($a > 1) {
                    ++$a;
                }
                // no break
            case 2:
            default:
        } // end switch

        return $a;
    }
}
<?php

namespace ZendCodingStandardTest\fixed;

use AnotherThrowableType;
use FirstThrowableType;
use OtherThrowableType;

class TryCatchFinally
{
    public function testTryCatchFinallyBlock(?string $foo): void
    {
        try {

            echo $foo;

        } catch(FirstThrowableType $e){
            echo $e->getMessage();
        } catch (  OtherThrowableType | AnotherThrowableType $e  ) {
            echo $e->getMessage();
        } finally
        {
            echo 'Done!';
        }
    }
}

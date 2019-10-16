<?php

declare(strict_types=1);

namespace ZendCodingStandardTest\fixed;

use AnotherThrowableType;
use Exception;
use FirstThrowableType;
use InvalidArgumentException;
use OtherThrowableType;
use Throwable;

class TryCatchFinally
{
    public function testTryCatchFinallyBlock(?string $foo): void
    {
        try {
            echo $foo;
        } catch (FirstThrowableType $e) {
            echo $e->getMessage();
        } catch (OtherThrowableType | AnotherThrowableType $e) {
            echo $e->getMessage();
        } finally {
            echo 'Done!';
        }
    }

    public function testUnreacableCatchBlock(): void
    {
        // All catch blocks MUST be reachable.

        try {
            $x = 1 + 2;
        } catch (Throwable $e) {
            echo $e;
        } catch (InvalidArgumentException $e) {
            echo $e; // unreachable!
        }
    }

    public function testCatchBlocksShouldUseThrowable(): void
    {
        try {
            $x = 1 + 2;
        } catch (Throwable $e) {
            // Should be Throwable
        }

        try {
            $x = 1 + 2;
        } catch (Exception $e) {
            // OK -> catching Throwable later
        } catch (Throwable $e) {
        }
    }
}

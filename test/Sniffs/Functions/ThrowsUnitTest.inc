<?php

namespace MyNamespace\Test;

use Exception;
use RuntimeException as Ex;

class ThrowUnitTest
{
    /**
     * Missing throw tag.
     */
    public function missingThrowTagFunctionWithComment()
    {
        throw new Exception();
    }

    public function missingThrowTagFunctionWithoutComment()
    {
        throw new Ex();
    }

    /**
     * Tag and token number mismatch.
     *
     * @throws Exception
     */
    public function oneMoreThrowsTagNeeded()
    {
        throw new Exception();
        throw new Ex();
    }

    /**
     * Tag and token number mismatch.
     *
     * @throws Exception
     * @throws Ex
     */
    public function oneLessThrowsTagNeeded()
    {
        throw new Exception();
    }

    /**
     * @throws \RuntimeException
     */
    public function fqcnInsteadOfImportedClassName()
    {
        throw new \RuntimeException();
    }

    /**
     * @throws Exception
     * @throws Ex
     */
    public function fqcnInCatchInsteadOfImportedClassName()
    {
        try {
        } catch(\RuntimeException | Ex | \Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @throws Ex
     */
    public function wrongExceptionTypeName()
    {
        throw new Exception();
    }

    /**
     * @throws \ErrorException
     * @throws Exception
     * @throws Ex
     */
    public function moreWrongExceptionTypeName()
    {
        throw new Exception();
        throw new Ex();
        throw new \InvalidArgumentException();
    }

    /**
     * @throws Exception if something
     * @throws Exception if something else
     */
    public function duplicateThrowInTag()
    {
        throw new Exception();
    }

    public function missingTagOnMethodOfAnonymousClass()
    {
        return new class () {
            public function missingThrowsTag() {
                throw new Ex();
            }
        };
    }

    /**
     * @throws Exception
     */
    public function rethrowException()
    {
        try {
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @throws Exception
     * @throws Ex
     */
    public function rethrowMultipleExceptions()
    {
        try {
        } catch(Exception | Ex $ex) {
            logError($ex);
            throw $ex;
        }
    }

    public function variableAndStaticException()
    {
        throw new Ex;

        try {
        } catch(\InvalidArgumentException $ex) {
            logError($ex);
            throw $ex;
        }
    }

    /**
     * @throws \Foo\Bar\Exception
     * @thorws AnotherException
     */
    public function namespacedException()
    {
        throw new \Foo\Bar\Exception();
        throw new \Foo\Bar\AnotherException();
    }

    /**
     * @throws Ex
     */
    public function catchAndThrowAnotherException()
    {
        try {
        } catch (Exception $e) {
            throw new Ex($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function hasThrowsTagsButDoesNotThrowAnything()
    {
    }

    /**
     * @throws Exception
     */
    abstract public function abstractCanHaveThrowsTags();

    public function throwNewClassFromVariable()
    {
        $e = Exception::class;
        throw new $e;
    }

    /**
     * @throws
     */
    public function missingExceptionTypeInTag()
    {
    }

    public function throwExceptionFromFactory()
    {
        throw ExceptionFacotry::create();
    }

    public function throwVariableAndException()
    {
        $e = new Exception();
        throw $e;
        throw new Exception();
    }

    /**
     * @throws Exception
     */
    public function throwVariableAndExceptionOK1()
    {
        $e = ExceptionFactory::createException();
        throw $e;
        throw new Exception();
    }

    /**
     * @throws Ex
     * @throws Exception
     */
    public function throwVariableAndExceptionOK2()
    {
        $e = ExceptionFactory::createException();
        throw $e;
        throw new Exception();
    }

    /**
     * @throws Exception
     * @throws Ex
     * @throws \InvalidArgumentException
     */
    public function valid1()
    {
        throw new Ex();
        throw new Exception();
        throw new Ex();
        throw new \InvalidArgumentException();
        throw new Exception();
    }

    public function valid2()
    {
        try {
            throw new Ex();
        } catch (Exception $e) {
        }
    }

    public function valid3()
    {
        return function() {
            throw new Ex();
        };
    }

    /**
     * @throws UnknownExceptionType
     */
    public function valid4()
    {
        throw $this->throwException();
    }

    /**
     * @throws UnknownException It cannot be determined.
     */
    public function valid5()
    {
        $ex = ExceptionFactory::create();
        throw $ex;
    }

    public function valid6()
    {
        return function () {
            return new class {
                /**
                 * @throws Exception
                 */
                public function x() {
                    throw new Exception();
                }
            };
        };
    }

    abstract public function abstractMethod();
}

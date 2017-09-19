<?php
/**
 * @license It is allowed to be here.
 */

class FunctionDisallowedTagUnitTest
{
    /**
     * Below tags are not allowed to be on in the doc-comment
     * of the method. Detecting tags is case insensitive.
     *
     * @author
     * @copyright
     * @license
     * @package
     * @subpackage
     * @version
     *
     * @Author
     * @VERSION
     */
    public function method1()
    {
    }

    /**
     * Below PHPUnit annotations are not allowed.
     * Corresponding function should be used instead.
     *
     * @expectedException
     * @expectedExceptionCode
     * @expectedExceptionMessage
     * @expectedExceptionMessageRegExp
     *
     * @ExpectedException Detecting tags is case insensitive.
     */
    public function testMethod()
    {
    }

    /**
     * @param int $a
     * @return void
     * @throws \Exception
     */
    public function allowedTags($a)
    {
    }

    public function withoutComment()
    {
    }
}

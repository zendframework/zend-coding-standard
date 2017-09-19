<?php
namespace MyApp\MyNamespace;

class Foo extends \PHPUnit\Framework\TestCase
{
    public function noDocComment() {}

    /**
     * @dataProvider name
     */
    public function noTestMethod() {}

    /**
     * @dataProvider name
     */
    public function testNoParams() {}

    /**
     * @dataProvider nameProvider
     * @param $a
     */
    public function testProviderSuffix($a) {}

    /**
     * @dataProvider
     * @param $a
     */
    public function testDataProviderNoName($a) {}

    /**
     * @dataProvider name1
     * @dataProvider name2
     * @param $a
     * @param $b
     */
    public function testMultipleDataProviders($a, $b) {}
}

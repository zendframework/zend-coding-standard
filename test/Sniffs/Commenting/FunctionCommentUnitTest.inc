<?php
namespace MyApp\MyNamespace;

class Foo
{
    // inline comment
    private function inlineComment() {}

    /**
     * @return void
     */

    private function emptyLine() {}

    /**
     * @throws \Exception
     * @param $a
     * @return void
     * @param $b
     * @myTag
     * @param $c
     * @anotherTag
     * @throws \RuntimeException
     * @param $d
     */
    private function tagsOrder($a, $b, $c, $d) {}

    /**
     * @see
     */
    private function seeMissingContent() {}

    /**
     * @dataProvider something
     * @param int $a
     *
     * @param int $b Param
     *     long description.
     *
     *
     * @return void
     *
     * @throws \Exception
     */
    private function removeBlankLines($a, $b) {}

    /**
     * @dataProvider something
     *
     *
     *
     * @param string $a
     */
    private function toManyEmptyLines($a) {}

    /**
     * @return array {
     *   @var bool $foo
     *   @var float $bar
     * }
     * @param array $a {
     *   @var string $isCache
     *   @var string $template
     * }
     */
    private function longParams($a) {}

    /**
     * @param array $a {
     *     @var string $hola
     * }
     *
     * @param int $b
     */
    private function noEmptyLineBetweenLongDefParamAndAnoterParam(array $a, $b) {}

    /**
     * @param array $a {
     *     @param string $hey
     * }
     */
    private function notAllowedNestedParam(array $a) {}

    /**
     * @param array $a {
     *     @var string $hi
     *     @var bool $required
     */
    private function missingClosingCurlyBRacket(array $a) {}

    /**
     * @param array $a {
     *   @var array {
     *     @var string $val
     *     @var bool $req
     *   }
     * }
     */
    private function twoLevelNesting(array $a) {}
}

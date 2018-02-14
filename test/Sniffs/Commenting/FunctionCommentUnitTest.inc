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
}

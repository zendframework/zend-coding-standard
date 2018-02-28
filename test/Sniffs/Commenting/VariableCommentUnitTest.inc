<?php

class Foo {
    // @var int
    public $bar;

    // inline comment is allowed here

    public $missingDocCommentInlineCommentBefore;

    public $missingDocComment;

    /** @var $missingType */
    public $missingType;

    /** @VAR int */
    public $noLowerCase;

    /**
     * @var array {
     *     @var bool $required
     *     @var string $label
     * }
     */
    public $array;

    /**
     * @var int
     * @var string
     */
    public $doubleVar;

    /**
     * @var array {
     *     @var int $int
     */
    public $invalidNested;

    /**
     * @var array {
     *     @var array $arr {
     *         @var string $label
     *         @param int $int Cannot be nested.
     *     }
     * }
     */
    public $multipleNested;
}

new class () {
    /** @param string */
    protected $param;

    /** @const float */
    public $const;
};

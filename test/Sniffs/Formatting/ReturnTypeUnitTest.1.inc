<?php
// @codingStandardsChangeSetting ZendCodingStandard.Formatting.ReturnType spacesBeforeColon 1
// @codingStandardsChangeSetting ZendCodingStandard.Formatting.ReturnType spacesAfterColon 2
// @codingStandardsChangeSetting ZendCodingStandard.Formatting.ReturnType spacesAfterNullable 0

function a() : int {}
function b(): ?  int {}
function c()
    :int{}
function d():
    ?int{}

// @codingStandardsChangeSetting ZendCodingStandard.Formatting.ReturnType spacesBeforeColon 2
// @codingStandardsChangeSetting ZendCodingStandard.Formatting.ReturnType spacesAfterColon 0
// @codingStandardsChangeSetting ZendCodingStandard.Formatting.ReturnType spacesAfterNullable 1

function e() : bool {}
function f(): ?  bool {}
function g()
    :bool{}
function h():
    ?bool {};

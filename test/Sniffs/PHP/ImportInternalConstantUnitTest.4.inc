<?php

namespace ImportInternalConstant;

$a = E_NOTICE;

$b = function ($x) use ($a) {
    return \E_WARNING;
};

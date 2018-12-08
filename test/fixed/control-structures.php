<?php

declare(strict_types=1);

switch (true) {
    case $a:
        break;
    case $b:
        if ($a > 1) {
            break;
        }
        break;
}

while ($a > 1) {
    for ($i = $a; $i < $b; ++$i) {
        if ($i % 2) {
            continue;
        }

        break 2;
    }
}

if ($a === $b + 1) {
    echo 1;
}

<?php

declare(strict_types=1);

switch (true) {
    case $a:
        continue;
    case $b:
        if ($a > 1) {
            continue;
        }
        break 1;
}

while ($a > 1) {
    for ($i = $a; $i < $b; ++$i) {
        if ($i % 2) {
            continue 1;
        }

        break   2;
    }
}

if ($a === ($b + 1)) {
    echo 1;
}

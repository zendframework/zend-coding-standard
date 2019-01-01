<?php

declare(strict_types=1);

while (! true) {
    echo 1;
}

do {
    echo 1;
} while (! false);

for (;;) {
    echo 'To infity and beyond';
}

echo 1;

$closure = function () {
};

$anonym = new class() {
};

<?php

declare(strict_types=1);

$hereDoc = <<<hereDoc
<?php

declare(strict_types=1);

class %s extends %s
{
}
hereDoc;

$hereDoc2 = <<<EOT
other...
EOT;

$nowDoc1 = <<<"NOW_DOC"
some content here
NOW_DOC;

$nowDoc2 = <<<'NOW'
content
NOW;

<?php

declare(strict_types=1);

$hereDoc = <<<hereDoc
<?php

declare(strict_types=1);

class %s extends %s
{
}
hereDoc;

$hereDoc2 = <<< EOT
other...
EOT;

$nowDoc1 = <<< "now_doc"
some content here
now_doc;

$nowDoc2 = <<< 'now'
content
now;

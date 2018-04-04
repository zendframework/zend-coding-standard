<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYRIGHT.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendCodingStandard;

use PHP_CodeSniffer\Config;

use function class_alias;
use function define;
use function explode;
use function file_exists;
use function file_get_contents;
use function json_decode;

class_alias(Tokenizer\MD::class, '\\PHP_CodeSniffer\\Tokenizers\\MD');

define('T_MD_LINE', 'ZFCS_T_MD_LINE');

if (file_exists('composer.json')) {
    $json = json_decode(file_get_contents('composer.json'), true);

    [$org, $repo] = explode('/', $json['name'] ?? '');

    if ($org && $repo) {
        Config::setConfigData('zfcs:org', $org);
        Config::setConfigData('zfcs:repo', $repo);
    }
}

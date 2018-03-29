<?php

declare(strict_types=1);

namespace ZendCodingStandard\Tokenizer;

use PHP_CodeSniffer\Tokenizers\Tokenizer;

use function explode;
use function preg_replace;

use const T_MD_LINE;

class MD extends Tokenizer
{
    /**
     * Creates an array of tokens when given some content.
     *
     * @param string $string The string to tokenize.
     * @return array
     */
    protected function tokenize($string)
    {
        $lines = explode("\n", $string);

        foreach ($lines as $n => &$line) {
            $line = [
                'content' => $line . "\n",
                'code' => T_MD_LINE,
                'type' => 'T_MD_LINE',
            ];
        }

        $line['content'] = preg_replace('/\n$/', '', $line['content']);

        return $lines;
    }

    /**
     * Performs additional processing after main tokenizing.
     */
    protected function processAdditional()
    {
    }
}

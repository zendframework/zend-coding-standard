<?php

declare(strict_types=1);

$array = [
    0,
    1,
    2,
    3,
    4,
    5,
];

$singleLine = [1 => 2];
$singleLine2 = [ 'foo' => 'bar' ];

$serializedRequest = [
    'method' => 'POST',
    'request_target' => '/foo/bar?baz=bat',
    'uri' => 'http://example.com/foo/bar?baz=bat',
    'protocol_version' => '1.1',
    'headers' => [
        'Host' => ['example.com'],
        'Accept' => ['application/json'],
        'X-Foo-Bar' => [
            'Baz',
            'Bat',
        ],
    ],
    'body' => '{"test":"value"}',
];

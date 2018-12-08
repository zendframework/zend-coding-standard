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

$bar = [
    'foo' => 1,
    'bar' => 2,
    'baz' => 3,
];

$singleLine  = [
    1 => 2,
];
$singleLine2 = ['foo' => 'bar'];

$serializedRequest = [
    'method'           => 'POST',
    'request_target'   => '/foo/bar?baz=bat',
    'uri'              => 'http://example.com/foo/bar?baz=bat',
    'protocol_version' => '1.1',
    'headers'          => [
        'Host'      => ['example.com'],
        'Accept'    => ['application/json'],
        'X-Foo-Bar' => [
            'Baz',
            'Bat',
        ],
    ],

    'body' => '{"test":"value"}',
];

// https://github.com/zendframework/zend-diactoros/blob/69dc20275fb8b9f7f8e05d556f6c0da5f36cac64/test/ServerRequestFactoryTest.php#L392-L398
$files = [
    'files' => [
        'tmp_name' => 'php://temp',
        'size'     => 0,
        'error'    => 0,
        'name'     => 'foo.bar',
        'type'     => 'text/plain',
    ],
];

// Long lines test
$config = [
    'dependencies' => [
        'factories' => [
            App\Domain\User\UserRepository::class => App\Domain\User\Persistence\DoctrineUserRepositoryFactory::class,
            App\Http\Auth\LoginHandler::class     => App\Http\Auth\LoginHandlerFactory::class,
            App\Http\Auth\LogoutHandler::class    => App\Http\Auth\LogoutHandlerFactory::class,

            App\Infrastructure\View\TemplateDefaultsMiddleware::class
                => App\Infrastructure\View\TemplateDefaultsMiddlewareFactory::class,
            App\Http\HomePageHandler::class   => App\Http\HomePageHandlerFactory::class,
            App\Http\StaticPageHandler::class => App\Http\StaticPageHandlerFactory::class,
        ],
    ],
];

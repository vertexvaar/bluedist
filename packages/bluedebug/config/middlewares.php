<?php

declare(strict_types=1);

use VerteXVaaR\BlueDebug\Middleware\CollectMessagesMiddleware;
use VerteXVaaR\BlueDebug\Middleware\CollectRequestDurationMiddleware;
use VerteXVaaR\BlueDebug\Middleware\CollectSessionMiddleware;
use VerteXVaaR\BlueDebug\Middleware\RenderDebugToolbarMiddleware;

return [
    'vertexvaar/bluedebug/collect-request-duration' => [
        'service' => CollectRequestDurationMiddleware::class,
        'before' => ['*'],
    ],
    'vertexvaar/bluedebug/debugger' => [
        'service' => RenderDebugToolbarMiddleware::class,
        'before' => ['vertexvaar/bluedebug/collect-request-duration'],
    ],
    'vertexvaar/bluedebug/collect-messages' => [
        'service' => CollectMessagesMiddleware::class,
        'before' => ['vertexvaar/bluesprints/actioncache'],
        'after' => ['*'],
    ],
    'vertexvaar/bluedebug/collect-session' => [
        'service' => CollectSessionMiddleware::class,
        'after' => ['vertexvaar/blueauth/authentication'],
        'before' => ['vertexvaar/bluesprints/routing'],
    ],
];

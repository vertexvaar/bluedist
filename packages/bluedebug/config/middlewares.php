<?php

declare(strict_types=1);


use VerteXVaaR\BlueDebug\Middleware\CollectorMiddleware;
use VerteXVaaR\BlueDebug\Middleware\RenderDebugToolbarMiddleware;
use VerteXVaaR\BlueDebug\Middleware\StopwatchMiddleware;

return [
    'vertexvaar/bluedebug/stopwatch' => [
        'service' => StopwatchMiddleware::class,
        'before' => ['*'],
    ],
    'vertexvaar/bluedebug/debugger' => [
        'service' => RenderDebugToolbarMiddleware::class,
        'before' => ['vertexvaar/bluedebug/stopwatch'],
    ],
    'vertexvaar/bluedebug/collector' => [
        'service' => CollectorMiddleware::class,
        'after' => ['*'],
    ],
];

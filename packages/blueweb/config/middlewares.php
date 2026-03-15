<?php

declare(strict_types=1);

use VerteXVaaR\BlueWeb\ActionCache\Middleware\ActionCacheMiddleware;
use VerteXVaaR\BlueWeb\Middleware\RequestIdentifierMiddleware;
use VerteXVaaR\BlueWeb\Routing\Middleware\RoutingMiddleware;

return [
    'vertexvaar/blueweb/requestidentifier' => [
        'service' => RequestIdentifierMiddleware::class,
        'before' => ['*'],
    ],
    'vertexvaar/bluesprints/routing' => [
        'service' => RoutingMiddleware::class,
    ],
    'vertexvaar/bluesprints/actioncache' => [
        'service' => ActionCacheMiddleware::class,
        'after' => ['vertexvaar/bluesprints/routing'],
    ],
];

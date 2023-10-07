<?php

declare(strict_types=1);

use VerteXVaaR\BlueSprints\Http\Server\Middleware\RoutingMiddleware;

return [
    'vertexvaar/bluesprints/routing' => [
        'service' => RoutingMiddleware::class,
    ]
];

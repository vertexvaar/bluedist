<?php

declare(strict_types=1);

use VerteXVaaR\BlueWeb\Routing\Middleware\RoutingMiddleware;

return [
    'vertexvaar/bluesprints/routing' => [
        'service' => RoutingMiddleware::class,
    ],
];

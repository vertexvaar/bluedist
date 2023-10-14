<?php

declare(strict_types=1);


use VerteXVaaR\BlueDebug\Middleware\DebugToolbarMiddleware;

return [
    'vertexvaar/bluedebug/debugger' => [
        'service' => DebugToolbarMiddleware::class,
        'after' => ['vertexvaar/bluesprints/routing']
    ]
];

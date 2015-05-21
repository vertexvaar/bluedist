<?php

use VerteXVaaR\BlueSprints\Controller\Frontend;
use VerteXVaaR\BlueSprints\Http\RequestInterface;

return [
    // safe methods
    RequestInterface::HTTP_METHOD_GET => [
        '/showPerson' => [
            'controller' => Frontend::class,
            'action' => 'showPerson',
        ],
        '/newPerson' => [
            'controller' => Frontend::class,
            'action' => 'newPerson',
        ],
        '/listPerson' => [
            'controller' => Frontend::class,
            'action' => 'listPerson',
        ],
        '/hello' => [
            'controller' => Frontend::class,
            'action' => 'hello',
        ],
        '.*' => [
            'controller' => Frontend::class,
            'action' => 'show',
        ],
    ],
    RequestInterface::HTTP_METHOD_HEAD => [],
    // not safe methods
    RequestInterface::HTTP_METHOD_POST => [
        '/createPerson' => [
            'controller' => Frontend::class,
            'action' => 'createPerson',
        ]
    ],
    RequestInterface::HTTP_METHOD_PUT => [],
    RequestInterface::HTTP_METHOD_DELETE => [],
];

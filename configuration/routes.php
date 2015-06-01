<?php

use VerteXVaaR\BlueSprints\Http\RequestInterface;
use VerteXVaaR\BlueWelcome\Controller\Welcome;

return [
    // safe methods
    RequestInterface::HTTP_METHOD_GET => [
        '/applyLeaves' => [
            'controller' => Welcome::class,
            'action' => 'applyLeaves',
        ],
        '/newTree' => [
            'controller' => Welcome::class,
            'action' => 'newTree',
        ],
        '/updateFruit' => [
            'controller' => Welcome::class,
            'action' => 'updateFruit',
        ],
        '/editFruit' => [
            'controller' => Welcome::class,
            'action' => 'editFruit'
        ],
        '/listFruits' => [
            'controller' => Welcome::class,
            'action' => 'listFruits',
        ],
        '.*' => [
            'controller' => Welcome::class,
            'action' => 'index',
        ],
    ],
    RequestInterface::HTTP_METHOD_HEAD => [],
    // not safe methods
    RequestInterface::HTTP_METHOD_POST => [
        '/deleteFruit' => [
            'controller' => Welcome::class,
            'action' => 'deleteFruit',
        ],
        '/addLeaf' => [
            'controller' => Welcome::class,
            'action' => 'addLeaf',
        ],
        '/growBranches' => [
            'controller' => Welcome::class,
            'action' => 'growBranches',
        ],
        '/createTree' => [
            'controller' => Welcome::class,
            'action' => 'createTree',
        ],
        '/updateFruit' => [
            'controller' => Welcome::class,
            'action' => 'updateFruit',
        ],
        '/createFruit' => [
            'controller' => Welcome::class,
            'action' => 'createFruit',
        ],
    ],
    RequestInterface::HTTP_METHOD_PUT => [],
    RequestInterface::HTTP_METHOD_DELETE => [],
];

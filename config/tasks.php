<?php

use VerteXVaaR\BlueDist\Task\ExampleTask;

return [
    'name' => [
        'task' => ExampleTask::class,
        'interval' => 10,
        'arguments' => [
            'first',
            'another arg',
        ],
    ],
];

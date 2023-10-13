<?php

use VerteXVaaR\BlueDist\Task\ExampleTask;

return [
    'name' => [
        'task' => ExampleTask::class,
        'interval' => 1,
        'arguments' => [
            'first',
            'another arg',
        ],
    ],
];

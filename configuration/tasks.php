<?php

use VerteXVaaR\BlueSprints\Task\Example2Task;
use VerteXVaaR\BlueSprints\Task\ExampleTask;

return [
    'task_custom_title' => [
        'task' => ExampleTask::class,
        'interval' => 1,
    ],
    'task_with_arguments' => [
        'task' => ExampleTask::class,
        'interval' => 2,
        'arguments' => [
            ' is going on',
        ],
    ],
    'task_using_cli_args' => [
        'task' => Example2Task::class,
        'interval' => 4,
    ],
];

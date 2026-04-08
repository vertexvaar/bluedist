<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueScheduler\DependencyInjection\SchedulerTaskCompilerPass;
use VerteXVaaR\BlueScheduler\Task\Task;

return static function (ContainerBuilder $container): void {
    $container->addCompilerPass(new SchedulerTaskCompilerPass());

    $container->registerForAutoconfiguration(Task::class)
        ->addTag('vertexvaar.bluesprints.scheduler.scheduled_task');
};

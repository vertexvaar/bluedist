<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueScheduler\DependencyInjection;

use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use VerteXVaaR\BlueScheduler\Attribute\ScheduledTask;
use VerteXVaaR\BlueScheduler\SchedulerTaskRegistry;

use function array_keys;

readonly class SchedulerTaskCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $scheduledTasks = [];

        $tasks = $container->findTaggedServiceIds('vertexvaar.bluesprints.scheduler.scheduled_task');
        foreach (array_keys($tasks) as $task) {
            $taskDefinition = $container->getDefinition($task);
            $taskClass = $taskDefinition->getClass();
            $taskReflection = new ReflectionClass($taskClass);
            $attributes = $taskReflection->getAttributes(ScheduledTask::class);
            foreach ($attributes as $attribute) {
                /** @var ScheduledTask $scheduledTask */
                $scheduledTask = $attribute->newInstance();
                $scheduledTasks[$taskClass][$scheduledTask->identifier] = [
                    'service' => new Reference($taskClass),
                    'config' => $scheduledTask->config,
                    'interval' => $scheduledTask->interval,
                ];
            }
        }

        $schedulerTaskRegistry = $container->getDefinition(SchedulerTaskRegistry::class);
        $schedulerTaskRegistry->setArgument('$tasks', $scheduledTasks);
    }
}

<?php

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueSprints\Environment\DependencyInjection\ConfigCompilerPass;
use VerteXVaaR\BlueSprints\Environment\DependencyInjection\EnvironmentCompilerPass;
use VerteXVaaR\BlueSprints\Environment\DependencyInjection\PathsCompilerPass;
use VerteXVaaR\BlueSprints\Http\DependencyInjection\MiddlewareCompilerPass;
use VerteXVaaR\BlueSprints\Mvcr\Controller\Controller;
use VerteXVaaR\BlueSprints\Mvcr\DependencyInjection\PublicServicePass;
use VerteXVaaR\BlueSprints\Routing\DependencyInjection\RouteCollectorCompilerPass;
use VerteXVaaR\BlueSprints\Scheduler\DependencyInjection\SchedulerTaskCompilerPass;
use VerteXVaaR\BlueSprints\Scheduler\Task\Task;
use VerteXVaaR\BlueSprints\Template\DependencyInjection\TemplateRendererCompilerPass;
use VerteXVaaR\BlueSprints\Translation\DependencyInjection\TranslationSourceCompilerPass;

return static function (ContainerBuilder $container): void {
    $container->addCompilerPass(new EnvironmentCompilerPass());
    $container->addCompilerPass(new PathsCompilerPass());
    $container->addCompilerPass(new ConfigCompilerPass());
    $container->addCompilerPass(new MiddlewareCompilerPass());
    $container->addCompilerPass(new RouteCollectorCompilerPass());
    $container->addCompilerPass(new TemplateRendererCompilerPass());
    $container->addCompilerPass(new SchedulerTaskCompilerPass());
    $container->addCompilerPass(new TranslationSourceCompilerPass());

    $container->registerForAutoconfiguration(Controller::class)
        ->addTag('vertexvaar.bluesprints.controller');
    $container->addCompilerPass(new PublicServicePass('vertexvaar.bluesprints.controller'));
    $container->registerForAutoconfiguration(Task::class)
        ->addTag('vertexvaar.bluesprints.scheduler.scheduled_task');
};

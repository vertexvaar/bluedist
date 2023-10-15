<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueSprints\Environment\DependencyInjection\ConfigCompilerPass;
use VerteXVaaR\BlueSprints\Environment\DependencyInjection\PathsCompilerPass;
use VerteXVaaR\BlueSprints\Http\DependencyInjection\MiddlewareCompilerPass;
use VerteXVaaR\BlueSprints\Mvcr\Controller\Controller;
use VerteXVaaR\BlueSprints\Routing\DependencyInjection\RouteCollectorCompilerPass;
use VerteXVaaR\BlueSprints\Template\DependencyInjection\TemplateRendererCompilerPass;

return static function (ContainerBuilder $container): void {
    $container->addCompilerPass(new PathsCompilerPass());
    $container->addCompilerPass(new ConfigCompilerPass());
    $container->addCompilerPass(new MiddlewareCompilerPass());
    $container->addCompilerPass(new RouteCollectorCompilerPass());
    $container->addCompilerPass(new TemplateRendererCompilerPass());

    $container->registerForAutoconfiguration(Controller::class)
        ->addTag('vertexvaar.bluesprints.controller');
};

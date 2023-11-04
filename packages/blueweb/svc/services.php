<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueWeb\Caching\DependencyInjection\ActionCacheCompilerPass;
use VerteXVaaR\BlueWeb\Controller\Controller;
use VerteXVaaR\BlueWeb\Middleware\DependencyInjection\MiddlewareCompilerPass;
use VerteXVaaR\BlueWeb\Routing\DependencyInjection\RouteCollectorCompilerPass;
use VerteXVaaR\BlueWeb\Template\DependencyInjection\TemplateRendererCompilerPass;

return static function (ContainerBuilder $container): void {
    $container->addCompilerPass(new MiddlewareCompilerPass());
    $container->addCompilerPass(new RouteCollectorCompilerPass('vertexvaar.bluesprints.controller'));
    $container->addCompilerPass(new TemplateRendererCompilerPass());
    $container->addCompilerPass(new ActionCacheCompilerPass('vertexvaar.bluesprints.controller'));

    $container->registerForAutoconfiguration(Controller::class)->addTag('vertexvaar.bluesprints.controller');
};

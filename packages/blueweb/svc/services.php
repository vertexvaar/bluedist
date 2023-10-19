<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueWeb\Controller\Controller;
use VerteXVaaR\BlueWeb\Middleware\DependencyInjection\MiddlewareCompilerPass;
use VerteXVaaR\BlueWeb\Routing\DependencyInjection\RouteCollectorCompilerPass;
use VerteXVaaR\BlueWeb\Template\DependencyInjection\TemplateRendererCompilerPass;

return static function (ContainerBuilder $container): void {
    $container->addCompilerPass(new MiddlewareCompilerPass());
    $container->addCompilerPass(new RouteCollectorCompilerPass());
    $container->addCompilerPass(new TemplateRendererCompilerPass());

    $container->registerForAutoconfiguration(Controller::class)->addTag('vertexvaar.bluesprints.controller');
};

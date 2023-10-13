<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueSprints\Environment\DependencyInjection\ConfigCompilerPass;
use VerteXVaaR\BlueSprints\Environment\DependencyInjection\EnvironmentCompilerPass;
use VerteXVaaR\BlueSprints\Environment\DependencyInjection\PathsCompilerPass;
use VerteXVaaR\BlueSprints\Http\DependencyInjection\MiddlewareCompilerPass;
use VerteXVaaR\BlueSprints\Mvc\Controller;
use VerteXVaaR\BlueSprints\Mvc\DependencyInjection\PublicServicePass;
use VerteXVaaR\BlueSprints\Routing\DependencyInjection\RouteCollectorCompilerPass;

return static function (ContainerBuilder $container): void {
    $container->addCompilerPass(new EnvironmentCompilerPass());
    $container->addCompilerPass(new PathsCompilerPass());
    $container->addCompilerPass(new ConfigCompilerPass());
    $container->addCompilerPass(new MiddlewareCompilerPass());
    $container->addCompilerPass(new RouteCollectorCompilerPass());

    $container->registerForAutoconfiguration(Controller::class)
        ->addTag('vertexvaar.bluesprints.controller');
    $container->addCompilerPass(new PublicServicePass('vertexvaar.bluesprints.controller'));
};

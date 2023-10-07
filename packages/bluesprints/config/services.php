<?php

use Composer\Composer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use VerteXVaaR\BlueSprints\Http\Server\Middleware\MiddlewareRegistry;

return static function (ContainerBuilder $containerBuilder): void {
    /** @var Composer $composer */
    $composer = $containerBuilder->get('composer');
    $installationManager = $composer->getInstallationManager();
    $packages = $composer->getRepositoryManager()->getLocalRepository()->getPackages();
    $middlewares = [];
    foreach ($packages as $package) {
        $installPath = $installationManager->getInstallPath($package);
        if (file_exists($installPath . '/config/middlewares.php')) {
            $packageMiddlewares = require $installPath . '/config/middlewares.php';
            foreach ($packageMiddlewares as $packageMiddleware) {
                $middlewares[] = new Reference($packageMiddleware['service']);
            }
        }
    }
    $registry = $containerBuilder->getDefinition(MiddlewareRegistry::class);
    $registry->setArgument('$middlewares', $middlewares);
};

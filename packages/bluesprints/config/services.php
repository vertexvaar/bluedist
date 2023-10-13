<?php

use Composer\Composer;
use Composer\Package\Package;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use VerteXVaaR\BlueContainer\Helper\PackageIterator;
use VerteXVaaR\BlueSprints\Config;
use VerteXVaaR\BlueSprints\Http\Server\Middleware\MiddlewareRegistry;
use VerteXVaaR\BlueSprints\Mvc\Controller;
use VerteXVaaR\BlueSprints\Mvc\DependencyInjection\PublicServicePass;
use VerteXVaaR\BlueSprints\Paths;
use VerteXVaaR\BlueSprints\Routing\DependencyInjection\RouteCollectorCompilerPass;

return static function (ContainerBuilder $containerBuilder): void {
    /** @var Composer $composer */
    $composer = $containerBuilder->get('composer');

    $packageIterator = new PackageIterator($composer);
    $middlewares = $packageIterator->iterate(static function (Package $package, string $installPath): array {
        $middlewares = [];
        if (file_exists($installPath . '/config/middlewares.php')) {
            $packageMiddlewares = require $installPath . '/config/middlewares.php';
            foreach ($packageMiddlewares as $packageMiddleware) {
                $middlewares[] = new Reference($packageMiddleware['service']);
            }
        }
        return $middlewares;
    });

    $packageConfig = $composer->getPackage()->getConfig();
    $config = $packageConfig['vertexvaar/bluesprints'];
    $pathsDefinition = $containerBuilder->getDefinition(Paths::class);
    $pathsDefinition->setArguments([
        '$logs' => $config['logs'] ?? 'var/logs',
        '$locks' => $config['locks'] ?? 'var/locks',
        '$cache' => $config['cache'] ?? 'var/cache',
        '$database' => $config['database'] ?? 'var/database',
        '$config' => $config['config'] ?? 'config',
        '$view' => $config['view'] ?? 'view',
        '$translations' => $config['translations'] ?? 'translations',
    ]);
    $pathsDefinition->setShared(true);
    $pathsDefinition->setPublic(true);

    $systemSettings = [];
    if (file_exists('config/system.php')) {
        $systemSettings = require 'config/system.php';
    }
    $configDefinition = $containerBuilder->getDefinition(Config::class);
    $configDefinition->setArguments([
        '$filePermissions' => $config['permissions']['files'] ?? 0660,
        '$folderPermissions' => $config['permissions']['folders'] ?? 0770,
    ]);
    $configDefinition->setShared(true);
    $configDefinition->setPublic(true);

    $packageMiddlewares = [];
    $middlewaresPath = $pathsDefinition->getArgument('$config') . '/middlewares.php';
    if (file_exists($middlewaresPath)) {
        $packageMiddlewares = require $middlewaresPath;
    }

    $middlewares = array_replace($packageMiddlewares, ...$middlewares);

    $registry = $containerBuilder->getDefinition(MiddlewareRegistry::class);
    $registry->setArgument('$middlewares', $middlewares);

    $containerBuilder->addCompilerPass(new RouteCollectorCompilerPass());

    $containerBuilder->registerForAutoconfiguration(Controller::class)
        ->addTag('vertexvaar.bluesprints.controller');
    $containerBuilder->addCompilerPass(new PublicServicePass('vertexvaar.bluesprints.controller'));
};

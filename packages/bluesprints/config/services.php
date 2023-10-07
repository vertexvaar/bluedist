<?php

use Composer\Composer;
use Composer\Package\Package;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use VerteXVaaR\BlueContainer\Helper\PackageIterator;
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

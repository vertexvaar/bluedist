<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Http\DependencyInjection;

use Composer\Composer;
use Composer\Package\Package;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use VerteXVaaR\BlueContainer\Helper\PackageIterator;
use VerteXVaaR\BlueSprints\Environment\Paths;
use VerteXVaaR\BlueSprints\Http\Server\Middleware\MiddlewareRegistry;

class MiddlewareCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        /** @var Composer $composer */
        $composer = $container->get('composer');

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

        $pathsDefinition = $container->getDefinition(Paths::class);

        $packageMiddlewares = [];
        $middlewaresPath = $pathsDefinition->getArgument('$config') . '/middlewares.php';
        if (file_exists($middlewaresPath)) {
            $packageMiddlewares = require $middlewaresPath;
        }

        $middlewares = array_replace($packageMiddlewares, ...$middlewares);

        $registry = $container->getDefinition(MiddlewareRegistry::class);
        $registry->setArgument('$middlewares', $middlewares);
    }

}

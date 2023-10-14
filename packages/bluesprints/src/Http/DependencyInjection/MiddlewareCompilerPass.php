<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Http\DependencyInjection;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\Package;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use VerteXVaaR\BlueContainer\Helper\PackageIterator;
use VerteXVaaR\BlueContainer\Service\DependencyOrderingService;
use VerteXVaaR\BlueSprints\Environment\Paths;
use VerteXVaaR\BlueSprints\Http\Server\Middleware\MiddlewareRegistry;

class MiddlewareCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        /** @var Composer $composer */
        $composer = $container->get('composer');
        /** @var IOInterface $io */
        $io = $container->get('io');

        $packageIterator = new PackageIterator($composer);
        $middlewares = $packageIterator->iterate(
            static function (Package $package, string $installPath) use ($io): array {
                $middlewares = [];
                if (file_exists($installPath . '/config/middlewares.php')) {
                    $io->write(
                        'Loading middlewares.php from ' . $package->getName(),
                        true,
                        IOInterface::VERBOSE
                    );
                    $packageMiddlewares = require $installPath . '/config/middlewares.php';
                    foreach ($packageMiddlewares as $index => $packageMiddleware) {
                        $middlewares[$index] = $packageMiddleware;
                    }
                }
                return $middlewares;
            }
        );

        $pathsDefinition = $container->getDefinition(Paths::class);

        $packageMiddlewares = [];
        $middlewaresPath = $pathsDefinition->getArgument('$config') . '/middlewares.php';
        if (file_exists($middlewaresPath)) {
            $packageMiddlewares = require $middlewaresPath;
        }

        $middlewares = array_replace($packageMiddlewares, ...$middlewares);

        $dependencyOrderingService = new DependencyOrderingService();
        $middlewares = $dependencyOrderingService->orderByDependencies($middlewares);

        $middlewareServices = [];
        foreach ($middlewares as $middleware) {
            $middlewareServices[] = new Reference($middleware['service']);
        }

        $registry = $container->getDefinition(MiddlewareRegistry::class);
        $registry->setArgument('$middlewares', $middlewareServices);
    }
}

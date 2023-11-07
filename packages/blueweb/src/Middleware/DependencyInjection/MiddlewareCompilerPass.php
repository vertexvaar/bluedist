<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueWeb\Middleware\DependencyInjection;

use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueContainer\Generated\PackageExtras;
use VerteXVaaR\BlueContainer\Helper\PackageIterator;
use VerteXVaaR\BlueContainer\Service\DependencyOrderingService;
use VerteXVaaR\BlueWeb\Middleware\MiddlewareChain;
use VerteXVaaR\BlueWeb\Middleware\MiddlewareRegistry;

use function CoStack\Lib\concat_paths;
use function file_exists;
use function sprintf;

class MiddlewareCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        /** @var PackageIterator $packageIterator */
        $packageIterator = $container->get('package_iterator');
        $middlewares = $packageIterator->map(
            fn(PackageInterface $package) => $this->loadMiddlewares($package, $container),
        );

        $dependencyOrderingService = new DependencyOrderingService();
        $middlewares = $dependencyOrderingService->orderByDependencies($middlewares);

        $middlewareServices = [];
        foreach ($middlewares as $middleware) {
            $service = $middleware['service'];
            $definition = $container->findDefinition($service);
            $definition->setPublic(true);
            $middlewareServices[] = $service;
        }

        $middlewareChain = $container->findDefinition(MiddlewareChain::class);
        $middlewareChain->setArgument('$middlewares', $middlewareServices);
    }

    private function loadMiddlewares(PackageInterface $package, ContainerBuilder $container): array
    {
        $io = $container->get('io');
        $packageExtras = $container->get(PackageExtras::class);

        $packageName = $package->getName();
        $absoluteMiddlewaresPath = $packageExtras->getPath($packageName, 'middlewares');

        if (null === $absoluteMiddlewaresPath) {
            $io->write(
                sprintf(
                    'Package %s does not define extra.vertexvaar/bluesprints.middlewares, skipping',
                    $package->getName(),
                ),
                true,
                IOInterface::VERY_VERBOSE,
            );
            return [];
        }
        $absoluteMiddlewaresFile = concat_paths($absoluteMiddlewaresPath, 'middlewares.php');

        if (!file_exists($absoluteMiddlewaresFile)) {
            $io->write(
                sprintf(
                    'Package %s defines extra.vertexvaar/bluesprints.config, but middlewares.php does not exist',
                    $package->getName(),
                ),
                true,
                IOInterface::VERY_VERBOSE,
            );
            return [];
        }

        $middlewares = [];
        $io->write(
            sprintf('Loading middlewares.php from package %s', $package->getName()),
            true,
            IOInterface::VERBOSE,
        );
        $packageMiddlewares = require $absoluteMiddlewaresFile;
        foreach ($packageMiddlewares as $index => $packageMiddleware) {
            $middlewares[$index] = $packageMiddleware;
        }
        return $middlewares;
    }
}

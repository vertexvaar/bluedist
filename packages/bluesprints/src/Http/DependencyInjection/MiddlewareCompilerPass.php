<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Http\DependencyInjection;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\Package;
use Composer\Package\PackageInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use VerteXVaaR\BlueContainer\Helper\PackageIterator;
use VerteXVaaR\BlueContainer\Service\DependencyOrderingService;
use VerteXVaaR\BlueSprints\Environment\Paths;
use VerteXVaaR\BlueSprints\Http\Server\Middleware\MiddlewareRegistry;

use function CoStack\Lib\concat_paths;
use function file_exists;
use function getenv;
use function sprintf;

class MiddlewareCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        /** @var Composer $composer */
        $composer = $container->get('composer');
        /** @var IOInterface $io */
        $io = $container->get('io');

        $packageIterator = new PackageIterator($composer);
        $middlewares = $packageIterator->iterate(
            fn(PackageInterface $package, string $installPath) => $this->loadMiddlewares($package, $installPath, $io)
        );

        $packageMiddlewares = $this->loadMiddlewares($composer->getPackage(), getenv('VXVR_BS_ROOT'), $io);
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

    private function loadMiddlewares(PackageInterface $package, string $installPath, IOInterface $io): array
    {
        $extra = $package->getExtra();
        if (!isset($extra['vertexvaar/bluesprints']['config'])) {
            $io->write(
                sprintf(
                    'Package %s does not define extra.vertexvaar/bluesprints.config, skipping',
                    $package->getName()
                ),
                true,
                IOInterface::VERY_VERBOSE
            );
            return [];
        }
        $configPath = $extra['vertexvaar/bluesprints']['config'];
        $absoluteMiddlewaresPath = concat_paths($installPath, $configPath, 'middlewares.php');

        if (!file_exists($absoluteMiddlewaresPath)) {
            $io->write(
                sprintf(
                    'Package %s defines extra.vertexvaar/bluesprints.config, but middlewares.php does not exist',
                    $package->getName()
                ),
                true,
                IOInterface::VERY_VERBOSE
            );
            return [];
        }

        $middlewares = [];
        $io->write(
            sprintf('Loading middlewares.php from package %s', $package->getName()),
            true,
            IOInterface::VERBOSE
        );
        $packageMiddlewares = require $absoluteMiddlewaresPath;
        foreach ($packageMiddlewares as $index => $packageMiddleware) {
            $middlewares[$index] = $packageMiddleware;
        }
        return $middlewares;
    }
}

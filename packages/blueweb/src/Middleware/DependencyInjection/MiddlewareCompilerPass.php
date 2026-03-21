<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueWeb\Middleware\DependencyInjection;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueFoundation\PackageExtras;
use VerteXVaaR\BlueFoundation\Service\DependencyOrderingService;
use VerteXVaaR\BlueWeb\Middleware\MiddlewareChain;

use function CoStack\Lib\concat_paths;
use function file_exists;
use function sprintf;

class MiddlewareCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        /** @var OutputInterface $output */
        $output = $container->get('_output');

        $output->writeln('Loading middlewares', OutputInterface::VERBOSITY_VERBOSE);

        $middlewares = $this->loadMiddlewares($container);

        $dependencyOrderingService = new DependencyOrderingService();
        $middlewares = $dependencyOrderingService->orderByDependencies($middlewares);

        $middlewareServices = [];
        foreach ($middlewares as $middleware) {
            $service = $middleware['service'];
            $definition = $container->findDefinition($service);
            $definition->setPublic(true);
            $middlewareServices[] = $service;
            $output->writeln(
                sprintf('  - Added middleware %s', $service),
                OutputInterface::VERBOSITY_DEBUG
            );
        }

        $middlewareChain = $container->findDefinition(MiddlewareChain::class);
        $middlewareChain->setArgument('$middlewares', $middlewareServices);

        $output->writeln('Loaded middlewares', OutputInterface::VERBOSITY_VERBOSE);
    }

    private function loadMiddlewares(ContainerBuilder $container): array
    {
        /** @var OutputInterface $output */
        $output = $container->get('_output');
        $packageExtras = $container->get(PackageExtras::class);

        $middlewares = [];

        foreach ($packageExtras->getPackageNames() as $packageName) {
            $absoluteMiddlewaresPath = $packageExtras->getPath($packageName, 'middlewares');

            if (null === $absoluteMiddlewaresPath) {
                $output->writeln(
                    sprintf(
                        'Package %s does not define extra.vertexvaar/bluesprints.middlewares, skipping',
                        $packageName,
                    ),
                    OutputInterface::VERBOSITY_VERY_VERBOSE,
                );
                continue;
            }
            $absoluteMiddlewaresFile = concat_paths($absoluteMiddlewaresPath, 'middlewares.php');

            if (!file_exists($absoluteMiddlewaresFile)) {
                $output->writeln(
                    sprintf(
                        'Package %s defines extra.vertexvaar/bluesprints.config, but middlewares.php does not exist',
                        $packageName,
                    ),
                    OutputInterface::VERBOSITY_VERY_VERBOSE,
                );
                continue;
            }

            $output->writeln(
                sprintf('Loading middlewares.php from package %s', $packageName),
                OutputInterface::VERBOSITY_VERBOSE,
            );
            $packageMiddlewares = require $absoluteMiddlewaresFile;
            foreach ($packageMiddlewares as $index => $packageMiddleware) {
                $middlewares[$index] = $packageMiddleware;
            }
        }
        return $middlewares;
    }
}

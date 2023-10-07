<?php

namespace VerteXVaaR\BlueSprints\Routing\DependencyInjection;

use Composer\Composer;
use Composer\IO\IOInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueSprints\Routing\Middleware\RoutingMiddleware;

use function file_exists;
use function sprintf;

class RouteCollectorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        /** @var Composer $composer */
        $composer = $container->get('composer');
        /** @var IOInterface $io */
        $io = $container->get('io');

        $packageConfig = $composer->getPackage()->getConfig();
        $configPath = $packageConfig['vertexvaar/bluesprints']['config'] ?? 'config';

        if (!file_exists($configPath . '/routes.php')) {
            $io->warning(sprintf('No routes found in "%s"', $configPath . '/routes.php'));
            $routes = [];
        } else {
            $routes = require $configPath . '/routes.php';
        }

        $definition = $container->getDefinition(RoutingMiddleware::class);
        $definition->setArgument('$routes', $routes);
    }
}

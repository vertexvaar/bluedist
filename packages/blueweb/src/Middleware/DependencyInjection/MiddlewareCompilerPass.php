<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueWeb\Middleware\DependencyInjection;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueFoundation\Service\DependencyOrderingService;
use VerteXVaaR\BlueWeb\Middleware\MiddlewareChain;

use function sprintf;

class MiddlewareCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        /** @var OutputInterface $output */
        $output = $container->get('_output');

        $output->writeln('Loading middlewares', OutputInterface::VERBOSITY_VERBOSE);

        $middlewares = [];

        $taggedServiceIds = $container->findTaggedServiceIds('blueweb.middleware');
        foreach ($taggedServiceIds as $id => $tags) {
            foreach ($tags as $tag) {
                $tag['service'] = $id;
                $middlewares[$tag['name']] = $tag;
            }
        }

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
                OutputInterface::VERBOSITY_DEBUG,
            );
        }

        $middlewareChain = $container->findDefinition(MiddlewareChain::class);
        $middlewareChain->setArgument('$middlewares', $middlewareServices);

        $output->writeln('Loaded middlewares', OutputInterface::VERBOSITY_VERBOSE);
    }
}

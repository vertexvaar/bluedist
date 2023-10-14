<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Environment\DependencyInjection;

use Composer\Composer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueSprints\Environment\Paths;

class PathsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        /** @var Composer $composer */
        $composer = $container->get('composer');
        $packageConfig = $composer->getPackage()->getExtra();
        $config = $packageConfig['vertexvaar/bluesprints'];

        $pathsDefinition = $container->getDefinition(Paths::class);
        $pathsDefinition->setArguments([
            '$logs' => $config['logs'] ?? 'var/logs',
            '$locks' => $config['locks'] ?? 'var/locks',
            '$cache' => $config['cache'] ?? 'var/cache',
            '$database' => $config['database'] ?? 'var/database',
            '$config' => $config['config'] ?? 'config',
            '$view' => $config['view'] ?? 'view',
            '$translations' => $config['translations'] ?? 'translations',
        ]);
    }

}

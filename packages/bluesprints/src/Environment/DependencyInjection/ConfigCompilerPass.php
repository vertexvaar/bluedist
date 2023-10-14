<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Environment\DependencyInjection;

use Composer\Composer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueSprints\Environment\Config;

class ConfigCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        /** @var Composer $composer */
        $composer = $container->get('composer');
        $packageConfig = $composer->getPackage()->getExtra();
        $config = $packageConfig['vertexvaar/bluesprints'];
        $permissions = $config['permissions'] ?? [];

        $configDefinition = $container->getDefinition(Config::class);
        $configDefinition->setArguments([
            '$filePermissions' => $permissions['files'] ?? 0660,
            '$folderPermissions' => $permissions['folders'] ?? 0770,
            '$cookieDomain' => $config['cookie']['domain'] ?? '',
            '$cookieAuthName' => $config['cookie']['authName'] ?? '',
        ]);
    }
}

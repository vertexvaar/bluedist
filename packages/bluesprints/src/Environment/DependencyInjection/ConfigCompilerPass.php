<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Environment\DependencyInjection;

use Composer\Composer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueSprints\Environment\Config;

use function CoStack\Lib\concat_paths;
use function file_exists;
use function getenv;

class ConfigCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        /** @var Composer $composer */
        $composer = $container->get('composer');
        $packageConfig = $composer->getPackage()->getExtra();

        $systemConfig = concat_paths(
            getenv('VXVR_BS_ROOT'),
            $packageConfig['vertexvaar/bluesprints']['config'] ?? 'config',
            'config.php'
        );

        if (file_exists($systemConfig)) {
            $config = require $systemConfig;
        } else {
            $config = Config::setOptions([]);
        }

        $arguments = [];
        foreach ($config as $name => $value) {
            $arguments['$' . $name] = $value;
        }

        $container->getDefinition(Config::class)->setArguments($arguments);
    }
}

<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Environment\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueSprints\Environment\Context;
use VerteXVaaR\BlueSprints\Environment\Environment;

use function getenv;

class EnvironmentCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition(Environment::class);
        $definition->setArgument('$context', Context::fromString((string)getenv('VXVR_BS_CONTEXT')));
    }
}

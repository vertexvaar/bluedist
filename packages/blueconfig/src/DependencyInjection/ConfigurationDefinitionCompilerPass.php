<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueConfig\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use VerteXVaaR\BlueConfig\Definition\DefinitionService;

use function array_keys;

class ConfigurationDefinitionCompilerPass implements CompilerPassInterface
{
    public function __construct(
        private readonly string $tagName,
    ) {
    }

    public function process(ContainerBuilder $container)
    {
        $definitionService = $container->findDefinition(DefinitionService::class);
        $definitions = $container->findTaggedServiceIds($this->tagName);
        $foundDefinitions = [];
        foreach (array_keys($definitions) as $definition) {
            $foundDefinitions[] = new Reference($definition);
        }
        $definitionService->setArgument('$definitions', $foundDefinitions);
    }
}

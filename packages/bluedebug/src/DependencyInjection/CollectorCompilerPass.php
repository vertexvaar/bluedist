<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use VerteXVaaR\BlueDebug\Collector\CollectorCollection;

use function array_keys;

readonly class CollectorCompilerPass implements CompilerPassInterface
{
    public function __construct(
        private string $tagName,
    ) {
    }

    public function process(ContainerBuilder $container)
    {
        $collectorCollection = $container->findDefinition(CollectorCollection::class);
        $collectors = $container->findTaggedServiceIds($this->tagName);
        foreach (array_keys($collectors) as $collector) {
            $collectorDefinition = $container->findDefinition($collector);
            $collectorDefinition->setShared(true);
            $collectorDefinition->setPublic(true);
            $collectorCollection->addMethodCall(
                'addCollector',
                [
                    new Reference($collector),
                ],
            );
        }
    }
}

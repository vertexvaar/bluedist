<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Mvc\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use function array_keys;

readonly class PublicServicePass implements CompilerPassInterface
{
    public function __construct(private string $tag)
    {
    }

    public function process(ContainerBuilder $container): void
    {
        foreach (array_keys($container->findTaggedServiceIds($this->tag)) as $service) {
            $container->getDefinition($service)->setPublic(true);
        }
    }

}

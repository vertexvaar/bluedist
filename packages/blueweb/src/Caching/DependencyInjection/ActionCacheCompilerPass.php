<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueWeb\Caching\DependencyInjection;

use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueWeb\Caching\Attributes\ActionCache;
use VerteXVaaR\BlueWeb\Caching\Middleware\ActionCacheMiddleware;

use function array_keys;
use function count;

class ActionCacheCompilerPass implements CompilerPassInterface
{
    public function __construct(
        private readonly string $tagName,
    ) {
    }

    public function process(ContainerBuilder $container)
    {
        $cachedActions = [];

        $services = $container->findTaggedServiceIds($this->tagName);
        foreach (array_keys($services) as $controllerService) {
            $controllerDefinition = $container->findDefinition($controllerService);
            $class = $controllerDefinition->getClass();
            $reflectionClass = new ReflectionClass($class);
            foreach ($reflectionClass->getMethods() as $reflectionMethod) {
                $reflectionCacheAttributes = $reflectionMethod->getAttributes(ActionCache::class);
                if (1 === count($reflectionCacheAttributes)) {
                    $reflectionCacheAttribute = $reflectionCacheAttributes[0];
                    /** @var ActionCache $cacheAttribute */
                    $cacheAttribute = $reflectionCacheAttribute->newInstance();
                    $cachedActions[$class][$reflectionMethod->getName()] = [
                        'ttl' => $cacheAttribute->ttl,
                        'params' => $cacheAttribute->params,
                    ];
                }
            }
        }

        $cachingMiddlewareDefinition = $container->findDefinition(ActionCacheMiddleware::class);
        $cachingMiddlewareDefinition->setArgument('$cachedActions', $cachedActions);
    }
}

<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueWeb\ActionCache\DependencyInjection;

use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueWeb\ActionCache\Attributes\ActionCache;
use VerteXVaaR\BlueWeb\ActionCache\Middleware\ActionCacheMiddleware;
use VerteXVaaR\BlueWeb\Enum\HttpMethod;
use VerteXVaaR\BlueWeb\Exception\ActionCacheWithoutRouteException;
use VerteXVaaR\BlueWeb\Exception\NonGetRouteActionCacheException;
use VerteXVaaR\BlueWeb\Routing\Attributes\Route;

use function array_keys;
use function count;
use function get_object_vars;

readonly class ActionCacheCompilerPass implements CompilerPassInterface
{
    public function __construct(
        private string $tagName,
    ) {}

    public function process(ContainerBuilder $container): void
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
                    $reflectionRouteAttributes = $reflectionMethod->getAttributes();
                    $routeAttributes = [];
                    foreach ($reflectionRouteAttributes as $reflectionRouteAttribute) {
                        $routeAttribute = $reflectionRouteAttribute->newInstance();
                        if ($routeAttribute instanceof Route) {
                            $routeAttributes[] = $routeAttribute;
                        }
                    }
                    if (empty($routeAttributes)) {
                        throw new ActionCacheWithoutRouteException($class, $reflectionMethod->getName());
                    }
                    foreach ($routeAttributes as $routeAttribute) {
                        if ($routeAttribute->method !== HttpMethod::GET) {
                            throw new NonGetRouteActionCacheException(
                                $class,
                                $reflectionMethod->getName(),
                                $routeAttribute->method->value,
                                $routeAttribute->path,
                            );
                        }
                    }

                    $reflectionCacheAttribute = $reflectionCacheAttributes[0];
                    /** @var ActionCache $cacheAttribute */
                    $cacheAttribute = $reflectionCacheAttribute->newInstance();
                    $cachedActions[$class][$reflectionMethod->getName()] = get_object_vars($cacheAttribute);
                }
            }
        }

        $cachingMiddlewareDefinition = $container->findDefinition(ActionCacheMiddleware::class);
        $cachingMiddlewareDefinition->setArgument('$cachedActions', $cachedActions);
    }
}

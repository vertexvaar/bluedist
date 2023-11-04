<?php

namespace VerteXVaaR\BlueWeb\Routing\DependencyInjection;

use Composer\IO\IOInterface;
use OutOfBoundsException;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueWeb\Routing\Attributes\Route;
use VerteXVaaR\BlueWeb\Routing\Middleware\RoutingMiddleware;

use function array_keys;
use function array_merge_recursive;
use function array_replace;
use function sprintf;

class RouteCollectorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        /** @var IOInterface $io */
        $io = $container->get('io');
        $io->write('Loading routes from controller attributes', true, IOInterface::VERBOSE);

        $compiledRoutes = [];
        $controllers = $container->findTaggedServiceIds('vertexvaar.bluesprints.controller');
        foreach (array_keys($controllers) as $controller) {
            $definition = $container->getDefinition($controller);
            $definition->setPublic(true);
            $controllerClass = $definition->getClass();
            try {
                $reflection = new ReflectionClass($controllerClass);
            } catch (ReflectionException $exception) {
                $io->writeError(
                    sprintf(
                        'Could not reflect controller "%s". Exception: %s',
                        $controllerClass,
                        $exception->getMessage(),
                    ),
                );
                continue;
            }
            $reflectionMethods = $reflection->getMethods();
            if (empty($reflectionMethods)) {
                $io->write(
                    sprintf('Controller "%s" does not define any methods', $controllerClass),
                    true,
                    IOInterface::VERBOSE,
                );
                continue;
            }
            foreach ($reflectionMethods as $reflectionMethod) {
                $attributes = $reflectionMethod->getAttributes(Route::class);
                foreach ($attributes as $attribute) {
                    /** @var Route $route */
                    $route = $attribute->newInstance();
                    $methodName = $reflectionMethod->getName();
                    $io->write(
                        sprintf(
                            'Found route [%d][%s] "%s" in controller "%s" method "%s"',
                            $route->priority,
                            $route->method,
                            $route->path,
                            $controllerClass,
                            $methodName,
                        ),
                        true,
                        IOInterface::VERBOSE,
                    );
                    $compiledRoutes[$route->method][$route->priority][$route->path] = [
                        'class' => \VerteXVaaR\BlueWeb\Routing\Route::class,
                        'controller' => $controllerClass,
                        'action' => $methodName,
                    ];
                }
            }
        }

        foreach ($compiledRoutes as $method => $paths) {
            krsort($paths);
            $compiledRoutes[$method] = array_replace([], ...$paths);
        }

        $definition = $container->getDefinition(RoutingMiddleware::class);
        try {
            $routes = $definition->getArgument('$routes');
        } catch (OutOfBoundsException $exception) {
            $routes = [];
        }
        $routes = array_merge_recursive($routes, $compiledRoutes);
        $definition->setArgument('$routes', $routes);

        $io->write('Loaded routes from controller attributes', true, IOInterface::VERBOSE);
    }
}

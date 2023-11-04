<?php

namespace VerteXVaaR\BlueWeb\Routing\DependencyInjection;

use Composer\IO\IOInterface;
use OutOfBoundsException;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueWeb\Routing\Attributes\Route;
use VerteXVaaR\BlueWeb\Routing\Attributes\RouteAttribute;
use VerteXVaaR\BlueWeb\Routing\Middleware\RoutingMiddleware;
use VerteXVaaR\BlueWeb\Routing\RouteEncapsulation;

use function array_keys;
use function array_merge;
use function get_class;
use function get_object_vars;
use function is_object;
use function sprintf;

class RouteCollectorCompilerPass implements CompilerPassInterface
{
    public function __construct(
        private readonly string $tagName,
    ) {
    }

    public function process(ContainerBuilder $container): void
    {
        /** @var IOInterface $io */
        $io = $container->get('io');
        $io->write('Loading routes from controller attributes', true, IOInterface::VERBOSE);

        $compiledRoutes = [];
        $controllers = $container->findTaggedServiceIds($this->tagName);
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
                $attributes = $reflectionMethod->getAttributes(
                    Route::class,
                    ReflectionAttribute::IS_INSTANCEOF,
                );
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
                    $compiledRoutes[$route->method][$route->priority][] = new RouteEncapsulation(
                        $route,
                        $controllerClass,
                        $methodName,
                    );
                }
            }
        }

        /** @var array<string, array<int, array<RouteEncapsulation>>> $compiledRoutes */
        foreach ($compiledRoutes as $method => $routesByPriority) {
            krsort($routesByPriority);
            $compiledRoutes[$method] = array_merge([], ...$routesByPriority);
        }

        $definition = $container->getDefinition(RoutingMiddleware::class);
        try {
            $routes = $definition->getArgument('$routes');
        } catch (OutOfBoundsException $exception) {
            $routes = [];
        }
        foreach ($compiledRoutes as $method => $encapsulatedRoutes) {
            foreach ($encapsulatedRoutes as $encapsulatedRoute) {
                $routes[$method][$encapsulatedRoute->route->path] = $this->getObjectVarsRecursive($encapsulatedRoute);
            }
        }
        $definition->setArgument('$routes', $routes);

        $io->write('Loaded routes from controller attributes', true, IOInterface::VERBOSE);
    }

    protected function getObjectVarsRecursive(object $object): array
    {
        $vars = get_object_vars($object);
        foreach ($vars as $index => $var) {
            if (is_object($var)) {
                $vars[$index] = [
                    'class' => get_class($var),
                    'vars' => $this->getObjectVarsRecursive($var),
                ];
            }
        }
        return $vars;
    }
}

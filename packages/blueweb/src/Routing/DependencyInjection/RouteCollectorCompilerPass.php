<?php

namespace VerteXVaaR\BlueWeb\Routing\DependencyInjection;

use Composer\IO\IOInterface;
use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueWeb\Routing\Attributes\Route;
use VerteXVaaR\BlueWeb\Routing\Attributes\RouteAttribute;
use VerteXVaaR\BlueWeb\Routing\Middleware\RoutingMiddleware;

use function array_keys;
use function array_merge;
use function get_class;
use function get_object_vars;
use function is_object;
use function krsort;
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

        $collectedRoutes = [];
        $compiledRoutes = [];
        $controllers = $container->findTaggedServiceIds($this->tagName);
        foreach (array_keys($controllers) as $controller) {
            $definition = $container->findDefinition($controller);
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
                    $collectedRoutes[$route->priority][] = [
                        'controller' => $controllerClass,
                        'action' => $methodName,
                        'class' => get_class($route),
                        'vars' => get_object_vars($route),
                    ];
                }
            }
        }
        krsort($collectedRoutes);
        $collectedRoutes = array_merge([], ...$collectedRoutes);

        $parser = new Std();
        $generator = new GroupCountBased();
        $routeCollector = new RouteCollector($parser, $generator);
        foreach ($collectedRoutes as $route) {
            $routeCollector->addRoute($route['vars']['method'], $route['vars']['path'], $route);
        }
        $data = $routeCollector->getData();

        $definition = $container->getDefinition(RoutingMiddleware::class);
        $definition->setArgument('$data', $data);

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

<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Collector;

use VerteXVaaR\BlueDebug\Rendering\CollectorRendering;

use function array_pop;
use function get_object_vars;
use function implode;
use function is_array;
use function is_object;

readonly class RouteCollector implements Collector
{
    public function __construct(
        private RequestCollector $requestCollector,
    ) {}

    public function render(): CollectorRendering
    {
        $routeEncapsulation = $this->requestCollector->getLastRequest()->getAttribute('route');
        $objectVars = $this->objectVarsRecursive($routeEncapsulation);
        $table = [];
        $this->dumpArray($objectVars, $table);
        return new CollectorRendering(
            'Route',
            $routeEncapsulation->controller . '::' . $routeEncapsulation->action,
            $table,
        );
    }

    protected function objectVarsRecursive(object $object): array
    {
        $vars = get_object_vars($object);
        foreach ($vars as $var => $val) {
            if (is_object($val)) {
                $vars[$var] = $this->objectVarsRecursive($val);
            }
        }
        return $vars;
    }

    protected function dumpArray(array $objectVars, array &$return, array $prefix = []): void
    {
        foreach ($objectVars as $name => $value) {
            $prefix[] = $name;
            if (is_array($value)) {
                $this->dumpArray($value, $return, $prefix);
            } else {
                $return[implode('.', $prefix)] = $value;
            }
            array_pop($prefix);
        }
    }
}

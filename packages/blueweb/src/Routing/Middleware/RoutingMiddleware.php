<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueWeb\Routing\Middleware;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use VerteXVaaR\BlueWeb\Routing\RouteEncapsulation;

use function preg_match;

class RoutingMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly array $routes)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();
        foreach ($this->routes[$method] as $pattern => $possibleRoute) {
            if (preg_match('~^' . $pattern . '$~', $path)) {
                $routeEncapsulation = new RouteEncapsulation(
                    new ($possibleRoute['route']['class'])(...$possibleRoute['route']['vars']),
                    $possibleRoute['controller'],
                    $possibleRoute['action'],
                );
                $request = $request->withAttribute('route', $routeEncapsulation);
                return $handler->handle($request);
            }
        }
        throw new Exception('Could not resolve a route for path "' . $path . '"', 1431887428);
    }
}

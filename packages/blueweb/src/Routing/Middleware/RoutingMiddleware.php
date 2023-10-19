<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueWeb\Routing\Middleware;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function preg_match;

class RoutingMiddleware implements MiddlewareInterface
{
    /**
     * @param array{'GET'|'HEAD'|'POST'|'PUT'|'DELETE'|'CONNECT'|'OPTIONS'|'TRACE': string} $routes
     */
    public function __construct(private readonly array $routes)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();
        foreach ($this->routes[$method] as $pattern => $possibleRoute) {
            if (preg_match('~^' . $pattern . '$~', $path)) {
                $class = $possibleRoute['class'];
                unset($possibleRoute['class']);
                $route = new ($class)(
                    $method,
                    $path,
                    ...$possibleRoute,
                );
                $request = $request->withAttribute('route', $route);
                return $handler->handle($request);
            }
        }
        throw new Exception('Could not resolve a route for path "' . $path . '"', 1431887428);
    }
}

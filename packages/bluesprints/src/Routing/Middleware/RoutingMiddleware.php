<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Routing\Middleware;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use VerteXVaaR\BlueSprints\Routing\Route;

use function preg_match;

class RoutingMiddleware implements MiddlewareInterface
{
    public const HTTP_METHOD_GET = 'GET';
    public const HTTP_METHOD_HEAD = 'HEAD';
    public const HTTP_METHOD_POST = 'POST';
    public const HTTP_METHOD_PUT = 'PUT';
    public const HTTP_METHOD_DELETE = 'DELETE';
    public const HTTP_METHOD_CONNECT = 'CONNECT';
    public const HTTP_METHOD_OPTIONS = 'OPTIONS';
    public const HTTP_METHOD_TRACE = 'TRACE';

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
                $route = new Route($method, $path, $possibleRoute['controller'], $possibleRoute['action']);
                $request = $request->withAttribute('route', $route);
                return $handler->handle($request);
            }
        }
        throw new Exception('Could not resolve a route for path "' . $path . '"', 1431887428);
    }
}

<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueWeb\Routing\Middleware;

use FastRoute\Dispatcher;
use FastRoute\Dispatcher\GroupCountBased;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TypeError;
use VerteXVaaR\BlueWeb\Exception\RouteAttributeConstructionException;
use VerteXVaaR\BlueWeb\Middleware\Attribute\AsMiddleware;
use VerteXVaaR\BlueWeb\Routing\RouteEncapsulation;

use function array_merge;

#[AsMiddleware('vertexvaar/bluesprints/routing')]
readonly class RoutingMiddleware implements MiddlewareInterface
{
    public function __construct(
        private array $data,
    ) {}

    /**
     * @throws RouteAttributeConstructionException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();

        $dispatcher = new GroupCountBased($this->data);
        $routingResult = $dispatcher->dispatch($method, $path);
        [$status, $routeHandler, $matches] = array_merge($routingResult, [null, null]);

        if (Dispatcher::NOT_FOUND === $status) {
            return new Response(404, [], 'Could not resolve a route for path "' . $path . '"');
        }
        if (Dispatcher::METHOD_NOT_ALLOWED === $status) {
            return new Response(405, [], 'Method not allowed for route "' . $path . '"');
        }

        try {
            $routeEncapsulation = new RouteEncapsulation(
                new ($routeHandler['class'])(...$routeHandler['vars']),
                $routeHandler['controller'],
                $routeHandler['action'],
                $matches,
            );
        } catch (TypeError $exception) {
            throw new RouteAttributeConstructionException(
                $routeHandler['class'],
                $routeHandler['controller'],
                $routeHandler['action'],
                $exception,
            );
        }

        $request = $request->withAttribute('route', $routeEncapsulation);

        return $handler->handle($request);
    }
}

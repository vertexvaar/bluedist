<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use VerteXVaaR\BlueSprints\Http\Server\Middleware\MiddlewareChain;
use VerteXVaaR\BlueSprints\Http\Server\Middleware\RoutingMiddleware;
use VerteXVaaR\BlueSprints\Http\Server\RequestHandler\ControllerDispatcher;

class Application
{
    protected const HANDLER = ControllerDispatcher::class;
    protected const MIDDLEWARE = [
        RoutingMiddleware::class,
    ];

    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $defaultHandler = self::HANDLER;
        $middlewareChain = new MiddlewareChain(new $defaultHandler());
        foreach (self::MIDDLEWARE as $middleware) {
            $middlewareChain->add(new $middleware);
        }

        return $middlewareChain->handle($request);
    }
}

<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use VerteXVaaR\BlueDebug\Collector\SessionCollector;
use VerteXVaaR\BlueWeb\Middleware\Attribute\AsMiddleware;

#[AsMiddleware(
    'vertexvaar/bluedebug/collect-session',
    ['vertexvaar/bluesprints/routing'],
    ['vertexvaar/blueauth/authentication']
)]
class CollectSessionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly SessionCollector $sessionCollector,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->sessionCollector->collect($request->getAttribute('session'));

        return $handler->handle($request);
    }
}

<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use VerteXVaaR\BlueDebug\Service\DebugCollector;

class CollectorMiddleware implements MiddlewareInterface
{
    public function __construct(private DebugCollector $collector)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->collector->collect('request', $request);
        $response = $handler->handle($request);
        $this->collector->collect('response', $response);
        return $response;
    }
}

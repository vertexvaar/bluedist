<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Http\Server\RequestHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewareHandler implements RequestHandlerInterface
{
    protected MiddlewareInterface $middleware;

    protected RequestHandlerInterface $handler;

    public function __construct(MiddlewareInterface $middleware, RequestHandlerInterface $handler)
    {
        $this->middleware = $middleware;
        $this->handler = $handler;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->middleware->process($request, $this->handler);
    }
}

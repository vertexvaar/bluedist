<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueWeb\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function current;
use function next;
use function reset;

class MiddlewareChain implements RequestHandlerInterface
{
    /**
     * @param array<MiddlewareInterface> $middlewares
     */
    public function __construct(
        private array $middlewares,
        private readonly RequestHandlerInterface $requestHandler,
    ) {
        reset($this->middlewares);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = current($this->middlewares);
        next($this->middlewares);
        if (false === $middleware) {
            return $this->requestHandler->handle($request);
        }
        return $middleware->process($request, $this);
    }
}

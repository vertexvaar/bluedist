<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueWeb\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function current;
use function end;
use function key;
use function next;
use function prev;
use function reset;

class MiddlewareChain implements RequestHandlerInterface
{
    /**
     * @param array<MiddlewareInterface> $middlewares
     */
    public function __construct(
        protected array $middlewares,
        protected readonly RequestHandlerInterface $requestHandler,
    ) {
        reset($this->middlewares);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = current($this->middlewares);

        if (false !== $middleware) {
            next($this->middlewares);
            try {
                return $middleware->process($request, $this);
            } finally {
                null === key($this->middlewares) ? end($this->middlewares) : prev($this->middlewares);
            }
        }

        return $this->requestHandler->handle($request);
    }
}

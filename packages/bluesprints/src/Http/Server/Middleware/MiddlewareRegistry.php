<?php

namespace VerteXVaaR\BlueSprints\Http\Server\Middleware;

use Psr\Http\Server\MiddlewareInterface;

readonly class MiddlewareRegistry
{
    /**
     * @param array<MiddlewareInterface> $middlewares
     */
    public function __construct(public array $middlewares)
    {
    }
}

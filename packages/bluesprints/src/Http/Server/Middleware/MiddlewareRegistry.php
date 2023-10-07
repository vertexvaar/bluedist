<?php

namespace VerteXVaaR\BlueSprints\Http\Server\Middleware;

class MiddlewareRegistry
{
    public function __construct(public readonly array $middlewares)
    {
    }
}

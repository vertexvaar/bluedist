<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use VerteXVaaR\BlueSprints\Http\Server\Middleware\MiddlewareChain;

class Application
{
    public function __construct(private readonly MiddlewareChain $middlewareChain)
    {
    }

    public function run(ServerRequestInterface $request): ResponseInterface
    {
        return $this->middlewareChain->handle($request);
    }
}

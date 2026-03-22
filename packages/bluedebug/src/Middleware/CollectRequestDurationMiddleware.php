<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use VerteXVaaR\BlueDebug\Collector\Stopwatch;
use VerteXVaaR\BlueWeb\Middleware\Attribute\AsMiddleware;

#[AsMiddleware('vertexvaar/bluedebug/collect-request-duration', ['*'])]
readonly class CollectRequestDurationMiddleware implements MiddlewareInterface
{
    public function __construct(
        private Stopwatch $stopwatch,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->stopwatch->start('request');
        $response = $handler->handle($request);
        $this->stopwatch->stop('request');
        return $response;
    }
}

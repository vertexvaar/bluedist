<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use VerteXVaaR\BlueDebug\Collector\RequestCollector;
use VerteXVaaR\BlueDebug\Collector\ResponseCollector;

readonly class CollectMessagesMiddleware implements MiddlewareInterface
{
    public function __construct(
        private RequestCollector $requestCollector,
        private ResponseCollector $responseCollector,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->requestCollector->collect($request);
        $response = $handler->handle($request);
        $this->responseCollector->collect($response);
        return $response;
    }
}

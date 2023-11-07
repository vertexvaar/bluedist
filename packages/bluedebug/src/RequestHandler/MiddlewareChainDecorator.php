<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\RequestHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use VerteXVaaR\BlueDebug\Collector\RequestCollector;
use VerteXVaaR\BlueDebug\Collector\ResponseCollector;
use VerteXVaaR\BlueWeb\Middleware\MiddlewareChain;

class MiddlewareChainDecorator extends MiddlewareChain
{
    public function __construct(
        private readonly MiddlewareChain $middlewareChain,
        private readonly RequestCollector $requestCollector,
        private readonly ResponseCollector $responseCollector,
    ) {
        parent::__construct(
            $this->middlewareChain->container,
            $this->middlewareChain->middlewares,
            $this->middlewareChain->requestHandler,
        );
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->requestCollector->collect($request);
        $response = parent::handle($request);
        $this->responseCollector->collect($response);
        return $response;
    }
}

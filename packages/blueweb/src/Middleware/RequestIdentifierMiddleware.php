<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueWeb\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;
use VerteXVaaR\BlueWeb\Middleware\Attribute\AsMiddleware;

#[AsMiddleware('vertexvaar/blueweb/requestidentifier', ['*'])]
readonly class RequestIdentifierMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uuid = Uuid::uuid4()->toString();
        $request = $request->withAttribute('requestId', $uuid);

        return $handler->handle($request);
    }
}

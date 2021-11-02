<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use VerteXVaaR\BlueSprints\Http\Server\RequestHandler\MiddlewareHandler;

use function array_reverse;

class MiddlewareChain
{
    /** @var RequestHandlerInterface */
    protected $requestHandler;

    /** @var MiddlewareInterface[] */
    protected $chain = [];

    public function __construct(RequestHandlerInterface $requestHandler) { $this->requestHandler = $requestHandler; }

    public function add(MiddlewareInterface $middleware): void { $this->chain[] = $middleware; }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $chain = $this->requestHandler;

        foreach (array_reverse($this->chain) as $middleware) {
            $chain = new MiddlewareHandler($middleware, $chain);
        }
        return $chain->handle($request);
    }
}

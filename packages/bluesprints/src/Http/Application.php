<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Http;

use GuzzleHttp\Psr7\ServerRequest;
use VerteXVaaR\BlueSprints\Http\Server\Middleware\MiddlewareChain;
use VerteXVaaR\BlueSprints\Http\Server\Middleware\RoutingMiddleware;
use VerteXVaaR\BlueSprints\Http\Server\RequestHandler\ControllerDispatcher;

use function header;

class Application
{
    protected const HANDLER = ControllerDispatcher::class;
    protected const MIDDLEWARE = [
        RoutingMiddleware::class,
    ];

    public function run()
    {
        $defaultHandler = self::HANDLER;
        $middlewareChain = new MiddlewareChain(new $defaultHandler());
        foreach (self::MIDDLEWARE as $middleware) {
            $middlewareChain->add(new $middleware);
        }

        $request = ServerRequest::fromGlobals();

        $response = $middlewareChain->handle($request);

        foreach ($response->getHeaders() as $name => $lines) {
            foreach ($lines as $line) {
                header($name . ':' . $line);
            }
        }
        $response->getBody()->rewind();
        echo $response->getBody()->getContents();
    }
}

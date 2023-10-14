<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Twig\Environment;

readonly class DebugToolbarMiddleware implements MiddlewareInterface
{

    public function __construct(private Environment $view)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $contents = $this->view->render('@vertexvaar_bluedebug/debug_toolbar.html.twig', [
            'route' => $request->getAttribute('route'),
            'authenticated' => $request->getAttribute('authenticated'),
            'username' => $request->getAttribute('username'),
        ]);

        $body = $response->getBody();
        $body->seek($body->getSize());
        $body->write($contents);

        return $response;
    }
}

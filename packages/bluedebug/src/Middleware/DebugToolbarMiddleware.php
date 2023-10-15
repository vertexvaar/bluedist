<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Twig\Environment as View;
use VerteXVaaR\BlueSprints\Environment\Context;
use VerteXVaaR\BlueSprints\Environment\Environment;

readonly class DebugToolbarMiddleware implements MiddlewareInterface
{
    public function __construct(private View $view, private Environment $environment)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if ($this->environment->context === Context::Production || $response->getStatusCode() >= 300) {
            return $response;
        }

        $contents = $this->view->render('@vertexvaar_bluedebug/debug_toolbar.html.twig', [
            'route' => $request->getAttribute('route'),
            'session' => $request->getAttribute('session'),
            'context' => $this->environment->context,
        ]);

        $body = $response->getBody();
        $body->seek($body->getSize());
        $body->write($contents);

        return $response;
    }
}

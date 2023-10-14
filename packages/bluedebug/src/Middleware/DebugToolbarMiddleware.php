<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use VerteXVaaR\BlueSprints\Mvc\TemplateRenderer;

readonly class DebugToolbarMiddleware implements MiddlewareInterface
{

    public function __construct(private TemplateRenderer $templateRenderer)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $this->templateRenderer->setRouteConfiguration(['controller' => 'DebugToolbarMiddleware']);

        $this->templateRenderer->setVariable('route', $request->getAttribute('route'));
        $this->templateRenderer->setVariable('authenticated', $request->getAttribute('authenticated'));
        $this->templateRenderer->setVariable('username', $request->getAttribute('username'));
        $contents = $this->templateRenderer->render('DebugToolbar');

        $body = $response->getBody();
        $body->seek($body->getSize());
        $body->write($contents);

        return $response;
    }
}

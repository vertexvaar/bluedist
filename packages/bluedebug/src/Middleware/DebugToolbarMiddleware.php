<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use VerteXVaaR\BlueFluid\Mvc\FluidTemplateRenderer;
use VerteXVaaR\BlueSprints\Environment\Paths;

use function getenv;
use function strlen;
use function substr;

readonly class DebugToolbarMiddleware implements MiddlewareInterface
{

    public function __construct()
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $viewPath = dirname(__DIR__, 2) . '/view';
        $viewPath = substr($viewPath, strlen(getenv('VXVR_BS_ROOT')));
        $paths = new Paths('', '', '', '', '', $viewPath, '');
        $templateRenderer = new FluidTemplateRenderer($paths);
        $templateRenderer->setRouteConfiguration(['controller' => 'DebugToolbarMiddleware']);

        $templateRenderer->setVariable('route', $request->getAttribute('route'));
        $templateRenderer->setVariable('authenticated', $request->getAttribute('authenticated'));
        $templateRenderer->setVariable('username', $request->getAttribute('username'));
        $contents = $templateRenderer->render('DebugToolbar');

        $body = $response->getBody();
        $body->seek($body->getSize());
        $body->write($contents);

        return $response;
    }
}

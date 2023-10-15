<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Twig\Environment as View;
use VerteXVaaR\BlueAuth\Mvcr\Model\Session;
use VerteXVaaR\BlueDebug\Service\DebugCollector;
use VerteXVaaR\BlueDebug\Service\Stopwatch;
use VerteXVaaR\BlueSprints\Environment\Context;
use VerteXVaaR\BlueSprints\Environment\Environment;
use VerteXVaaR\BlueSprints\Routing\Route;

readonly class RenderDebugToolbarMiddleware implements MiddlewareInterface
{
    public function __construct(
        private View $view,
        private Environment $environment,
        private DebugCollector $collector,
        private Stopwatch $stopwatch,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if ($this->environment->context === Context::Production || $response->getStatusCode() >= 300) {
            return $response;
        }

        /** @var ServerRequestInterface $collectedRequest */
        $collectedRequest = $this->collector->getItem('request');
        /** @var ResponseInterface $collectedResponse */
        $collectedResponse = $this->collector->getItem('response');

        /** @var Route $collectedRoute */
        $collectedRoute = $collectedRequest->getAttribute('route');
        /** @var Session $collectedSession */
        $collectedSession = $collectedRequest->getAttribute('session');

        $contents = $this->view->render('@vertexvaar_bluedebug/debug_toolbar.html.twig', [
            'request' => $collectedRequest,
            'response' => $collectedResponse,
            'route' => $collectedRoute,
            'session' => $collectedSession,
            'context' => $this->environment->context,
            'stopwatch' => $this->stopwatch,
        ]);

        $body = $response->getBody();
        $body->seek($body->getSize());
        $body->write($contents);

        return $response;
    }
}

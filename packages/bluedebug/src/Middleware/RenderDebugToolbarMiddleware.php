<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Middleware;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\SimpleCache\CacheInterface;
use Twig\Environment as View;
use VerteXVaaR\BlueAuth\Mvcr\Model\Session;
use VerteXVaaR\BlueAuth\Routing\Attributes\AuthorizedRoute;
use VerteXVaaR\BlueDebug\Decorator\CacheDecorator;
use VerteXVaaR\BlueDebug\Service\CollectedQuery;
use VerteXVaaR\BlueDebug\Service\DebugCollector;
use VerteXVaaR\BlueDebug\Service\QueryCollector;
use VerteXVaaR\BlueDebug\Service\QueryExecution;
use VerteXVaaR\BlueDebug\Service\Stopwatch;
use VerteXVaaR\BlueSprints\Environment\Context;
use VerteXVaaR\BlueSprints\Environment\Environment;

use VerteXVaaR\BlueWeb\Routing\Attributes\Route;

use VerteXVaaR\BlueWeb\Routing\RouteEncapsulation;

use function serialize;
use function unserialize;

readonly class RenderDebugToolbarMiddleware implements MiddlewareInterface
{
    public function __construct(
        private View $view,
        private Environment $environment,
        private DebugCollector $collector,
        private Stopwatch $stopwatch,
        private QueryCollector $queryCollector,
        private CacheInterface $cache,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if ($this->environment->context === Context::Production) {
            return $response;
        }

        /** @var null|ServerRequestInterface $collectedRequest */
        $collectedRequest = $this->collector->getItem('request');
        /** @var null|ResponseInterface $collectedResponse */
        $collectedResponse = $this->collector->getItem('response');
        /** @var null|Route $collectedRoute */
        $collectedRoute = $collectedRequest?->getAttribute('route');
        /** @var null|Session $collectedSession */
        $collectedSession = $collectedRequest?->getAttribute('session');

        if ($response->getStatusCode() >= 300) {
            $lastRequest = [
                'request' => $collectedRequest,
                'response' => $collectedResponse,
                'route' => $collectedRoute,
                'session' => $collectedSession,
                'context' => $this->environment->context,
                'stopwatch' => $this->stopwatch,
                'queryCollector' => $this->queryCollector,
                'cacheCalls' => CacheDecorator::getCalls(),
            ];
            $this->cache->set('last_request', serialize($lastRequest));
            return $response;
        }

        $lastRequest = $this->cache->get('last_request');
        $this->cache->delete('last_request');
        if (null !== $lastRequest) {
            $lastRequest = unserialize($lastRequest, [
                'allowed_classes' => [
                    ServerRequest::class,
                    Response::class,
                    Route::class,
                    AuthorizedRoute::class,
                    RouteEncapsulation::class,
                    Session::class,
                    Stopwatch::class,
                    QueryCollector::class,
                    DebugCollector::class,
                    CollectedQuery::class,
                    QueryExecution::class,
                ],
            ]);
        }

        $contents = $this->view->render('@vertexvaar_bluedebug/debug_toolbar.html.twig', [
            'request' => $collectedRequest,
            'response' => $collectedResponse,
            'route' => $collectedRoute,
            'session' => $collectedSession,
            'context' => $this->environment->context,
            'stopwatch' => $this->stopwatch,
            'queryCollector' => $this->queryCollector,
            'lastRequest' => $lastRequest,
            'cacheCalls' => CacheDecorator::getCalls(),
        ]);

        $body = $response->getBody();
        $body->seek($body->getSize());
        $body->write($contents);

        return $response;
    }
}

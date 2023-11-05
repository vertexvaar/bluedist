<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\SimpleCache\CacheInterface;
use Twig\Environment as View;
use VerteXVaaR\BlueDebug\Collector\CollectorCollection;
use VerteXVaaR\BlueDebug\CollectorRendering;
use VerteXVaaR\BlueSprints\Environment\Context;
use VerteXVaaR\BlueSprints\Environment\Environment;

use function array_unshift;
use function is_string;
use function iterator_to_array;
use function serialize;
use function strlen;
use function unserialize;

readonly class RenderDebugToolbarMiddleware implements MiddlewareInterface
{
    public function __construct(
        private View $view,
        private Environment $environment,
        private CacheInterface $cache,
        private CollectorCollection $collectorCollection,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if ($this->environment->context === Context::Production) {
            return $response;
        }

        /** @var array<CollectorRendering> $collectorRenderings */
        $collectorRenderings = iterator_to_array($this->collectorCollection->render());

        if ($response->getStatusCode() >= 300) {
            $table = [];
            foreach ($collectorRenderings as $rendering) {
                $table['<b>' . $rendering->title . '</b>'] = $rendering->shortInformation;
                foreach ($rendering->popupTable ?? [] as $key => $value) {
                    $table[$key] = $value;
                }
            }
            $contents = new CollectorRendering(
                'Previous Request',
                (string)$response->getStatusCode(),
                $table,
            );
            $this->cache->set('last_request', serialize($contents));
            return $response;
        }

        $lastRequest = $this->cache->get('last_request');
        if (is_string($lastRequest) && strlen($lastRequest) > 10) {
            $lastRequest = unserialize($lastRequest, ['allowed_classes' => [CollectorRendering::class]]);
            array_unshift($collectorRenderings, $lastRequest);
        }
        $contents = $this->view->render('@vertexvaar_bluedebug/debug_toolbar.html.twig', [
            'collectorRenderings' => $collectorRenderings,
        ]);
        $this->cache->delete('last_request');

        $body = $response->getBody();
        $body->seek($body->getSize());
        $body->write($contents);

        return $response;
    }
}

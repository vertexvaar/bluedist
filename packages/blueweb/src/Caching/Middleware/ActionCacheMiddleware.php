<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueWeb\Caching\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\SimpleCache\CacheInterface;
use VerteXVaaR\BlueSprints\Environment\Context;
use VerteXVaaR\BlueSprints\Environment\Environment;
use VerteXVaaR\BlueWeb\Routing\Route;

use function CoStack\Lib\concat_paths;
use function hash;
use function json_encode;
use function ksort;
use function str_replace;
use function version_compare;

use const JSON_THROW_ON_ERROR;

class ActionCacheMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly array $cachedActions,
        private readonly CacheInterface $cache,
        private readonly Environment $environment,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $request->getAttribute('route');
        if (!isset($this->cachedActions[$route->controller][$route->action])) {
            $response = $handler->handle($request);
            if ($this->environment->context !== Context::Production) {
                $response = $response->withAddedHeader('X-Bluesprints-Cache', 'Uncached');
            }
            return $response;
        }

        $cacheHash = $this->getCacheHash($route, $request);
        $cacheKey = concat_paths(
            'actions',
            str_replace('\\', '.', $route->controller),
            $route->action,
            $cacheHash,
        );

        if (!$this->forceCacheEvasion($request) && $contents = $this->cache->get($cacheKey)) {
            return $this->createResponseFromContent($contents);
        }

        $response = $handler->handle($request);
        if ($response->getStatusCode() !== 200) {
            return $response->withAddedHeader('X-Bluesprints-Cache', 'Not cacheable');
        }

        $ttl = $this->cacheResponseContents($response, $route, $cacheKey);

        if ($this->environment->context !== Context::Production) {
            $response = $response->withAddedHeader('X-Bluesprints-Cache', 'Set for ' . $ttl);
        }

        return $response;
    }

    protected function forceCacheEvasion(ServerRequestInterface $request): bool
    {
        $cacheHeader = version_compare($request->getProtocolVersion(), '1.0', '==')
            ? $request->getHeaderLine('pragma')
            : $request->getHeaderLine('cache-control');
        return $cacheHeader === 'no-cache';
    }

    protected function createResponseFromContent(string $contents): ResponseInterface
    {
        $response = new Response();
        $response->getBody()->write($contents);
        if ($this->environment->context !== Context::Production) {
            $response = $response->withAddedHeader('X-Bluesprints-Cache', 'Cached');
        }
        return $response;
    }

    protected function getCacheHash(Route $route, ServerRequestInterface $request): string
    {
        $params = $this->cachedActions[$route->controller][$route->action]['params'];
        if (empty($params)) {
            return 'none';
        }

        $queryParams = $request->getQueryParams();
        $cacheKeyParams = [];
        foreach ($params as $name) {
            $cacheKeyParams[$name] = $queryParams[$name] ?? null;
        }
        ksort($cacheKeyParams);
        return hash('sha1', json_encode($cacheKeyParams, JSON_THROW_ON_ERROR));
    }

    protected function cacheResponseContents(ResponseInterface $response, Route $route, string $cacheKey): mixed
    {
        $body = $response->getBody();
        $body->rewind();
        $contents = $body->getContents();

        $ttl = $this->cachedActions[$route->controller][$route->action]['ttl'];

        $this->cache->set($cacheKey, $contents, $ttl);

        return $ttl;
    }
}

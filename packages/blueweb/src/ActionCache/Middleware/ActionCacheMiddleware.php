<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueWeb\ActionCache\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\SimpleCache\CacheInterface;
use VerteXVaaR\BlueSprints\Environment\Context;
use VerteXVaaR\BlueSprints\Environment\Environment;
use VerteXVaaR\BlueWeb\ActionCache\Attributes\ActionCache;
use VerteXVaaR\BlueWeb\Routing\RouteEncapsulation;

use function array_merge;
use function array_unique;
use function CoStack\Lib\concat_paths;
use function hash;
use function json_encode;
use function ksort;
use function serialize;
use function str_replace;
use function unserialize;
use function version_compare;

use const JSON_THROW_ON_ERROR;

class ActionCacheMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly array $cachedActions,
        private readonly CacheInterface $cache,
        private readonly Environment $environment,
    ) {}

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

        $forceCacheEvasion = $this->forceCacheEvasion($request);
        if (!$forceCacheEvasion && $this->cache->has($cacheKey)) {
            $responseCacheEntry = unserialize(
                $this->cache->get($cacheKey),
                ['allowed_classes' => [ResponseCacheEntry::class]]
            );
            return $this->createResponseFromContent($responseCacheEntry);
        }

        $response = $handler->handle($request);
        if ($response->getStatusCode() !== 200) {
            return $response->withAddedHeader('X-Bluesprints-Cache', 'Not cacheable');
        }

        $ttl = $this->cacheResponseContents($response, $route, $cacheKey);

        if ($this->environment->context !== Context::Production) {
            $headerLine = 'Set for ' . $ttl;
            if ($forceCacheEvasion) {
                $headerLine .= ' (forced)';
            }
            $response = $response->withAddedHeader('X-Bluesprints-Cache', $headerLine);
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

    protected function createResponseFromContent(ResponseCacheEntry $responseCacheEntry): ResponseInterface
    {
        $response = new Response();
        $response->getBody()->write($responseCacheEntry->contents);
        if ($this->environment->context !== Context::Production) {
            foreach ($responseCacheEntry->headers as $header => $value) {
                $response = $response->withAddedHeader($header, $value);
            }
            $response = $response->withAddedHeader('X-Bluesprints-Cache', 'Cached');
        }
        return $response;
    }

    protected function getCacheHash(RouteEncapsulation $route, ServerRequestInterface $request): string
    {
        $actionCache = new ActionCache(...$this->cachedActions[$route->controller][$route->action]);
        if (empty($actionCache->params) && empty($actionCache->matches)) {
            return 'none';
        }

        if ($actionCache->interchangeableParams) {
            $cacheKeyValues = $this->getInterchangeableCacheKeyValues($request, $actionCache);
        } else {
            $cacheKeyValues = $this->getDistinctCacheKeyValues($request, $actionCache);
        }

        return hash('sha1', json_encode($cacheKeyValues, JSON_THROW_ON_ERROR));
    }

    protected function getInterchangeableCacheKeyValues(
        ServerRequestInterface $request,
        ActionCache $actionCache,
    ): array {
        $keys = array_unique(array_merge($actionCache->params, $actionCache->matches));

        $queryParams = $request->getQueryParams();
        $matches = $request->getAttribute('route')->matches;

        $values = [];
        foreach ($keys as $key) {
            $values[$key] = $matches[$key] ?? $queryParams[$key] ?? null;
        }
        ksort($values);

        return $values;
    }

    protected function getDistinctCacheKeyValues(ServerRequestInterface $request, ActionCache $actionCache): array
    {
        $queryParams = $request->getQueryParams();
        $cacheKeyParams = [];
        foreach ($actionCache->params as $name) {
            $cacheKeyParams[$name] = $queryParams[$name] ?? null;
        }
        ksort($cacheKeyParams);

        $givenMatches = $request->getAttribute('route')->matches;
        $cacheKeyMatches = [];
        foreach ($actionCache->matches as $name) {
            $cacheKeyMatches[$name] = $givenMatches[$name] ?? null;
        }
        ksort($cacheKeyMatches);
        return [$cacheKeyParams, $cacheKeyMatches];
    }

    protected function cacheResponseContents(
        ResponseInterface $response,
        RouteEncapsulation $route,
        string $cacheKey,
    ): mixed {
        $headers = $response->getHeaders();
        $body = $response->getBody();
        $body->rewind();
        $contents = $body->getContents();

        $ttl = $this->cachedActions[$route->controller][$route->action]['ttl'];

        $this->cache->set($cacheKey, serialize(new ResponseCacheEntry($contents, $headers)), $ttl);

        return $ttl;
    }
}

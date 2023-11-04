<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueWeb\Caching\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use VerteXVaaR\BlueSprints\Cache\Cache;

use function CoStack\Lib\concat_paths;
use function str_replace;

class ActionCacheMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly array $cachedActions,
        private readonly Cache $cache,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $request->getAttribute('route');
        if (!isset($this->cachedActions[$route->controller][$route->action])) {
            return $handler->handle($request);
        }
        $cacheKey = concat_paths('actions', str_replace('\\', '.', $route->controller), $route->action);

        if ($this->cache->has($cacheKey)) {
            $contents = $this->cache->get($cacheKey);
            if ($contents !== null) {
                $response = new Response();
                $response->getBody()->write($contents);
                return $response;
            }
        }

        $response = $handler->handle($request);

        $body = $response->getBody();
        $body->rewind();
        $contents = $body->getContents();
        $this->cache->set($cacheKey, $contents, $this->cachedActions[$route->controller][$route->action]);

        return $response;
    }
}

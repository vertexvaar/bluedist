<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueAuth\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\SimpleCache\CacheInterface;
use VerteXVaaR\BlueAuth\Service\AuthenticationService;
use VerteXVaaR\BlueConfig\Config;

use function CoStack\Lib\inet_match_range;

readonly class LoginRedirectionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private CacheInterface $cache,
        private AuthenticationService $authenticationService,
        private Config $config,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        if (
            303 === $response->getStatusCode()
            && '/login' === $request->getUri()->getPath()
        ) {
            $session = $request->getAttribute('session');
            $cacheKey = 'previousRequest/' . $session->identifier;
            if (
                $session->isAuthenticated()
                && $this->cache->has($cacheKey)
            ) {
                $previousRequestUri = $this->cache->get($cacheKey);
                $this->cache->delete($cacheKey);
                return new Response(303, ['Location' => $previousRequestUri]);
            }
            return $response;
        }
        if (
            403 === $response->getStatusCode()
            && 'GET' === $request->getMethod()
        ) {
            $session = $request->getAttribute('session');
            if ($session->isAuthenticated()) {
                return $response;
            }
            $session = $this->authenticationService->forcePersistentSession($request);
            $cacheKey = 'previousRequest/' . $session->identifier;
            $uri = $request->getUri();
            $remoteAddress = $request->getServerParams()['REMOTE_ADDR'];
            if (inet_match_range($remoteAddress, $this->config->get('security.trustedProxyAddresses'))) {
                if ($request->hasHeader('X-Forwarded-Proto')) {
                    $forwardedProtocol = $request->getHeaderLine('X-Forwarded-Proto');
                    if ('https' === $forwardedProtocol) {
                        $uri = $uri->withScheme('https');
                    }
                }
                if ($request->hasHeader('X-Forwarded-Port')) {
                    $forwardedPort = $request->getHeaderLine('X-Forwarded-Port');
                    $uri = $uri->withPort($forwardedPort);
                }
            }
            $this->cache->set($cacheKey, $uri);
            $response = new Response(303, ['Location' => '/login'], 'Login required');
        }
        return $response;
    }
}

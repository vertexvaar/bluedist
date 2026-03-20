<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueAuth\Middleware;

use CoStack\Lib\Exceptions\Ipv6SupportDisabledException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
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

        if ($this->isRedirectToLogin($request, $response)) {
            $previousRequestUri = $this->getPreviousRequest($request);
            if ($previousRequestUri) {
                return new Response(303, ['Location' => $previousRequestUri]);
            }

            return $response;
        }

        if ($this->isAuthenticationRequired($request, $response)) {
            $session = $request->getAttribute('session');

            // If authenticated but authentication is required, the user does not have the required authorization.
            if ($session->isAuthenticated()) {
                return $response;
            }

            $this->setPreviousRequest($request);

            return new Response(303, ['Location' => '/login'], 'Login required');
        }

        return $response;
    }

    protected function isRedirectToLogin(ServerRequestInterface $request, ResponseInterface $response): bool
    {
        if (303 !== $response->getStatusCode()) {
            return false;
        }
        if ('/login' !== $request->getUri()->getPath()) {
            return false;
        }
        return true;
    }

    protected function getPreviousRequest(ServerRequestInterface $request): ?string
    {
        $session = $request->getAttribute('session');

        if (!$session->isAuthenticated()) {
            return null;
        }

        $cacheKey = 'previousRequest/' . $session->identifier;
        if (!$this->cache->has($cacheKey)) {
            return null;
        }

        $previousRequestUri = $this->cache->get($cacheKey);
        $this->cache->delete($cacheKey);
        return $previousRequestUri;
    }

    protected function isAuthenticationRequired(ServerRequestInterface $request, ResponseInterface $response): bool
    {
        if (403 !== $response->getStatusCode()) {
            return false;
        }
        if ('GET' !== $request->getMethod()) {
            return false;
        }
        return true;
    }

    protected function setPreviousRequest(ServerRequestInterface $request): void
    {
        $session = $this->authenticationService->forcePersistentSession($request);

        $url = $this->getRedirectUrl($request);

        $this->cache->set('previousRequest/' . $session->identifier, $url);
    }

    protected function getRedirectUrl(ServerRequestInterface $request): UriInterface
    {
        $uri = $request->getUri();

        $remoteAddress = $request->getServerParams()['REMOTE_ADDR'];

        try {
            if (!inet_match_range($remoteAddress, $this->config->get('security.trustedProxyAddresses'))) {
                return $uri;
            }
        } catch (Ipv6SupportDisabledException) {
            return $uri;
        }

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

        return $uri;
    }
}

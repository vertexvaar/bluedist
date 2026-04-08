<?php

namespace VerteXVaaR\BlueWeb\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use VerteXVaaR\BlueConfig\Config;
use VerteXVaaR\BlueWeb\Exception\NoAllowedOriginException;
use VerteXVaaR\BlueWeb\Exception\ServerNameNotDeterminedException;
use VerteXVaaR\BlueWeb\Middleware\Attribute\AsMiddleware;

use function implode;
use function in_array;
use function preg_match;

#[AsMiddleware('vertexvaar/blueweb/cors', ['*'])]
readonly class CorsMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected Config $config,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $origin = $request->getHeaderLine('Origin');

        // Handle preflight request
        if ($request->getMethod() === 'OPTIONS') {
            $response = new Response();
            return $this->addCorsHeaders($request, $response, $origin);
        }

        $response = $handler->handle($request);

        return $this->addCorsHeaders($request, $response, $origin);
    }

    private function addCorsHeaders(
        ServerRequestInterface $request,
        ResponseInterface $response,
        string $origin,
    ): ResponseInterface {
        $allowedOrigins = $this->getAllowedOrigins($request);

        // Allow specific origins or wildcard
        if (in_array('*', $allowedOrigins, true)) {
            $response = $response->withHeader('Access-Control-Allow-Origin', '*');
        } elseif (in_array($origin, $allowedOrigins, true)) {
            $response = $response->withHeader('Access-Control-Allow-Origin', $origin);
        }

        return $response
            ->withHeader('Access-Control-Allow-Methods', implode(', ', $this->config->get('cors.allowedMethods')))
            ->withHeader('Access-Control-Allow-Headers', implode(', ', $this->config->get('cors.allowedHeaders')))
            ->withHeader('Vary', 'Origin');
    }

    protected function getTrustedServerName(ServerRequestInterface $request): string
    {
        $serverName = $request->getServerParams()['SERVER_NAME'];

        foreach ($this->config->get('trustedServerNames') as $trustedServerName) {
            if ($trustedServerName === '*') {
                return $serverName;
            }
            if ($trustedServerName === $serverName) {
                return $serverName;
            }
            if ($trustedServerName === 'SERVER_NAME') {
                return $serverName;
            }
            if (1 === preg_match($trustedServerName, $serverName)) {
                return $serverName;
            }
        }
        throw new ServerNameNotDeterminedException();
    }

    protected function getAllowedOrigins(ServerRequestInterface $request): array
    {
        $allowedOrigins = [];

        foreach ($this->config->get('cors.allowedOrigins') as $allowedOrigin) {
            if ($allowedOrigin === '*') {
                return ['*'];
            }
            if ($allowedOrigin === '_trustedServerName') {
                $allowedOrigins[] = $this->getTrustedServerName($request);
            }
        }

        if (empty($allowedOrigins)) {
            throw new NoAllowedOriginException();
        }

        return $allowedOrigins;
    }
}

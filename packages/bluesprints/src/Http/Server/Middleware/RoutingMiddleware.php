<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Http\Server\Middleware;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use VerteXVaaR\BlueSprints\Utility\Files;

use function array_merge_recursive;
use function preg_match;

class RoutingMiddleware implements MiddlewareInterface
{
    public const CONFIGURATION_FILENAME = 'config/routes.php';
    public const HTTP_METHOD_GET = 'GET';
    public const HTTP_METHOD_HEAD = 'HEAD';
    public const HTTP_METHOD_POST = 'POST';
    public const HTTP_METHOD_PUT = 'PUT';
    public const HTTP_METHOD_DELETE = 'DELETE';
    public const HTTP_METHOD_CONNECT = 'CONNECT';
    public const HTTP_METHOD_OPTIONS = 'OPTIONS';
    public const HTTP_METHOD_TRACE = 'TRACE';

    /** @var array[][] */
    protected array $configuration = [
        self::HTTP_METHOD_GET => [],
        self::HTTP_METHOD_HEAD => [],
        self::HTTP_METHOD_POST => [],
        self::HTTP_METHOD_PUT => [],
        self::HTTP_METHOD_DELETE => [],
        self::HTTP_METHOD_CONNECT => [],
        self::HTTP_METHOD_OPTIONS => [],
        self::HTTP_METHOD_TRACE => [],
    ];

    public function __construct()
    {
        if (!Files::fileExists(self::CONFIGURATION_FILENAME)) {
            throw new Exception('The Router configuration does not exist', 1431886993);
        }
        $this->configuration = array_merge_recursive(
            $this->configuration,
            Files::requireFile(self::CONFIGURATION_FILENAME)
        );
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        foreach ($this->configuration[$request->getMethod()] as $pattern => $possibleRoute) {
            if (preg_match('~^' . $pattern . '$~', $path)) {
                $request = $request->withAttribute('route', $possibleRoute);
                return $handler->handle($request);
            }
        }
        throw new Exception('Could not resolve a route for path "' . $path . '"', 1431887428);
    }
}

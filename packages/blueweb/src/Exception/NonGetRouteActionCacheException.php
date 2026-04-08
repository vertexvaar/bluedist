<?php

namespace VerteXVaaR\BlueWeb\Exception;

use JetBrains\PhpStorm\Pure;
use Throwable;
use VerteXVaaR\BlueSprints\BluesprintsException;

use function sprintf;

class NonGetRouteActionCacheException extends BluesprintsException
{
    private const string MESSAGE = 'Can not cache actions with non-GET routes. Method: %s::%s Conflicting Route: %s: %s';
    public const int CODE = 1775606422;

    #[Pure]
    public function __construct(
        public readonly string $class,
        public readonly string $method,
        public readonly string $httpMethod,
        public readonly string $path,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            sprintf(self::MESSAGE, $this->class, $this->method, $this->httpMethod, $this->path),
            self::CODE,
            $previous,
        );
    }
}

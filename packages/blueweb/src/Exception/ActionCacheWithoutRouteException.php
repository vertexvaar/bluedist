<?php

namespace VerteXVaaR\BlueWeb\Exception;

use JetBrains\PhpStorm\Pure;
use Throwable;
use VerteXVaaR\BlueSprints\BluesprintsException;

use function sprintf;

class ActionCacheWithoutRouteException extends BluesprintsException
{
    private const string MESSAGE = 'Can not cache an action without a route annotation. Method: %s::%s';
    public const int CODE = 1775606421;

    #[Pure]
    public function __construct(
        public readonly string $class,
        public readonly string $method,
        ?Throwable $previous = null,
    ) {
        parent::__construct(sprintf(self::MESSAGE, $this->class, $this->method), self::CODE, $previous);
    }
}

<?php

namespace VerteXVaaR\BlueWeb\Exception;

use Throwable;
use VerteXVaaR\BlueSprints\BluesprintsException;

use function sprintf;

class RouteAttributeConstructionException extends BluesprintsException
{
    private const string MESSAGE = 'Route attribute %s defined of method %s::%s is invalid: %s';
    public const int CODE = 1773689072;

    public function __construct(
        readonly public string $attribute,
        readonly public string $class,
        readonly public string $method,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            sprintf(self::MESSAGE, $this->attribute, $this->class, $this->method, $previous->getMessage()),
            self::CODE,
            $previous,
        );
    }
}

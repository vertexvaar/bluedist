<?php

namespace VerteXVaaR\BlueAdmin\Exception;

use JetBrains\PhpStorm\Pure;
use Throwable;
use VerteXVaaR\BlueSprints\BluesprintsException;

use function sprintf;

class UnsupportedBulkActionMethodException extends BluesprintsException
{
    private const string MESSAGE = 'HttpMethod::%s can not be used for BulkAction';
    public const int CODE = 1775606401;

    #[Pure]
    public function __construct(
        public readonly string $method,
        ?Throwable $previous = null,
    ) {
        parent::__construct(sprintf(self::MESSAGE, $this->method), self::CODE, $previous);
    }
}

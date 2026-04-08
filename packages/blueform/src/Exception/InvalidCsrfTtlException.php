<?php

namespace VerteXVaaR\BlueForm\Exception;

use JetBrains\PhpStorm\Pure;
use Throwable;
use VerteXVaaR\BlueSprints\BluesprintsException;

class InvalidCsrfTtlException extends BluesprintsException
{
    private const string MESSAGE = 'TTL must be greater than 0.';
    public const int CODE = 1775606407;

    #[Pure]
    public function __construct(
        ?Throwable $previous = null,
    ) {
        parent::__construct(self::MESSAGE, self::CODE, $previous);
    }
}

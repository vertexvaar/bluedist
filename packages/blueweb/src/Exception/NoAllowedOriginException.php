<?php

namespace VerteXVaaR\BlueWeb\Exception;

use JetBrains\PhpStorm\Pure;
use Throwable;
use VerteXVaaR\BlueSprints\BluesprintsException;

class NoAllowedOriginException extends BluesprintsException
{
    private const string MESSAGE = 'No allowed origin could be identified';
    public const int CODE = 1775606419;

    #[Pure]
    public function __construct(
        ?Throwable $previous = null,
    ) {
        parent::__construct(self::MESSAGE, self::CODE, $previous);
    }
}

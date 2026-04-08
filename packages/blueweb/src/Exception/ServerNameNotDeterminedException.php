<?php

namespace VerteXVaaR\BlueWeb\Exception;

use JetBrains\PhpStorm\Pure;
use Throwable;
use VerteXVaaR\BlueSprints\BluesprintsException;

class ServerNameNotDeterminedException extends BluesprintsException
{
    private const string MESSAGE = 'Could not reliably determine the server\'s name';
    public const int CODE = 1775606418;

    #[Pure]
    public function __construct(
        ?Throwable $previous = null,
    ) {
        parent::__construct(self::MESSAGE, self::CODE, $previous);
    }
}

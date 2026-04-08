<?php

namespace VerteXVaaR\BlueForm\Exception;

use JetBrains\PhpStorm\Pure;
use Throwable;
use VerteXVaaR\BlueSprints\BluesprintsException;

class EmptyCsrfTokenIdException extends BluesprintsException
{
    private const string MESSAGE = 'Token ID must not be empty.';
    public const int CODE = 1775606404;

    #[Pure]
    public function __construct(
        ?Throwable $previous = null,
    ) {
        parent::__construct(self::MESSAGE, self::CODE, $previous);
    }
}

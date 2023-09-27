<?php

namespace VerteXVaaR\BlueSprints\Http\Exception;

use JetBrains\PhpStorm\Pure;
use VerteXVaaR\BlueSprints\BluesprintsException;

class OutputAlreadySentException extends BluesprintsException
{
    private const MESSAGE = 'Headers already sent.';
    public const CODE = 1695812166;

    #[Pure] public function __construct(?Throwable $previous = null)
    {
        parent::__construct(self::MESSAGE, self::CODE, $previous);
    }
}

<?php

namespace VerteXVaaR\BlueForm\Exception;

use JetBrains\PhpStorm\Pure;
use Throwable;
use VerteXVaaR\BlueSprints\BluesprintsException;

class EmptyCsrfSecretException extends BluesprintsException
{
    private const string MESSAGE = 'Secret must not be empty.';
    public const int CODE = 1775606405;

    #[Pure]
    public function __construct(
        ?Throwable $previous = null,
    ) {
        parent::__construct(self::MESSAGE, self::CODE, $previous);
    }
}

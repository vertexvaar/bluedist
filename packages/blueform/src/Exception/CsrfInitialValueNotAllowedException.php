<?php

namespace VerteXVaaR\BlueForm\Exception;

use JetBrains\PhpStorm\Pure;
use Throwable;
use VerteXVaaR\BlueSprints\BluesprintsException;

class CsrfInitialValueNotAllowedException extends BluesprintsException
{
    private const string MESSAGE = 'Calling setInitialValue on CSRF is not allowed';
    public const int CODE = 1775606409;

    #[Pure]
    public function __construct(
        ?Throwable $previous = null,
    ) {
        parent::__construct(self::MESSAGE, self::CODE, $previous);
    }
}

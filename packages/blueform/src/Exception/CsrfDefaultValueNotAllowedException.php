<?php

namespace VerteXVaaR\BlueForm\Exception;

use JetBrains\PhpStorm\Pure;
use Throwable;
use VerteXVaaR\BlueSprints\BluesprintsException;

class CsrfDefaultValueNotAllowedException extends BluesprintsException
{
    private const string MESSAGE = 'Calling setDefaultValue on CSRF is not allowed';
    public const int CODE = 1775606408;

    #[Pure]
    public function __construct(
        ?Throwable $previous = null,
    ) {
        parent::__construct(self::MESSAGE, self::CODE, $previous);
    }
}

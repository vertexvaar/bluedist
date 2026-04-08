<?php

namespace VerteXVaaR\BlueSeed\Exception;

use JetBrains\PhpStorm\Pure;
use Throwable;
use VerteXVaaR\BlueSprints\BluesprintsException;

use function sprintf;

class SeederDoesNotExistException extends BluesprintsException
{
    private const string MESSAGE = 'Seeder "%s" does not exist.';
    public const int CODE = 1775606416;

    #[Pure]
    public function __construct(
        public readonly string $identifier,
        ?Throwable $previous = null,
    ) {
        parent::__construct(sprintf(self::MESSAGE, $this->identifier), self::CODE, $previous);
    }
}

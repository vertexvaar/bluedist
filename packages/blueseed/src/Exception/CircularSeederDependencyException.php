<?php

namespace VerteXVaaR\BlueSeed\Exception;

use JetBrains\PhpStorm\Pure;
use Throwable;
use VerteXVaaR\BlueSprints\BluesprintsException;

use function sprintf;

class CircularSeederDependencyException extends BluesprintsException
{
    private const string MESSAGE = 'Circular dependency detected: %s';
    public const int CODE = 1775606417;

    #[Pure]
    public function __construct(
        public readonly string $chain,
        ?Throwable $previous = null,
    ) {
        parent::__construct(sprintf(self::MESSAGE, $this->chain), self::CODE, $previous);
    }
}

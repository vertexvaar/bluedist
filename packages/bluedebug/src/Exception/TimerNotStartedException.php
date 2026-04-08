<?php

namespace VerteXVaaR\BlueDebug\Exception;

use JetBrains\PhpStorm\Pure;
use Throwable;
use VerteXVaaR\BlueSprints\BluesprintsException;

use function sprintf;

class TimerNotStartedException extends BluesprintsException
{
    private const string MESSAGE = 'Timer not started for %s';
    public const int CODE = 1775606403;

    #[Pure]
    public function __construct(
        public readonly string $name,
        ?Throwable $previous = null,
    ) {
        parent::__construct(sprintf(self::MESSAGE, $this->name), self::CODE, $previous);
    }
}

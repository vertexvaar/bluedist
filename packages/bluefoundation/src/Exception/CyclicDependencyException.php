<?php

namespace VerteXVaaR\BlueFoundation\Exception;

use JetBrains\PhpStorm\Pure;
use Throwable;
use VerteXVaaR\BlueSprints\BluesprintsException;

use function sprintf;

class CyclicDependencyException extends BluesprintsException
{
    private const string MESSAGE = 'Your dependencies have cycles. That will not work out. Cycles found: %s';
    public const int CODE = 1381960494;

    #[Pure]
    public function __construct(
        public readonly string $cycles,
        ?Throwable $previous = null,
    ) {
        parent::__construct(sprintf(self::MESSAGE, $this->cycles), self::CODE, $previous);
    }
}

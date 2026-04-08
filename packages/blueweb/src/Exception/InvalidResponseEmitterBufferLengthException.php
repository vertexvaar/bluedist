<?php

namespace VerteXVaaR\BlueWeb\Exception;

use JetBrains\PhpStorm\Pure;
use Throwable;
use VerteXVaaR\BlueSprints\BluesprintsException;

use function sprintf;

class InvalidResponseEmitterBufferLengthException extends BluesprintsException
{
    private const string MESSAGE = 'Buffer length for `%s` must be greater than zero; received `%d`.';
    public const int CODE = 1775606420;

    #[Pure]
    public function __construct(
        public readonly string $class,
        public readonly int $bufferLength,
        ?Throwable $previous = null,
    ) {
        parent::__construct(sprintf(self::MESSAGE, $this->class, $this->bufferLength), self::CODE, $previous);
    }
}

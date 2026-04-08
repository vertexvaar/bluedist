<?php

namespace VerteXVaaR\BlueSprints\Exception;

use JetBrains\PhpStorm\Pure;
use Throwable;
use VerteXVaaR\BlueSprints\BluesprintsException;

use function sprintf;

class DirectoryNotCreatedException extends BluesprintsException
{
    private const string MESSAGE = 'Directory "%s" was not created';
    public const int CODE = 1775606415;

    #[Pure]
    public function __construct(
        public readonly string $directory,
        ?Throwable $previous = null,
    ) {
        parent::__construct(sprintf(self::MESSAGE, $this->directory), self::CODE, $previous);
    }
}

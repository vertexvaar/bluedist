<?php

namespace VerteXVaaR\BlueFoundation\Exception;

use JetBrains\PhpStorm\Pure;
use Throwable;
use VerteXVaaR\BlueSprints\BluesprintsException;

use function sprintf;

class PackagePathInvalidException extends BluesprintsException
{
    private const string MESSAGE = 'The path "%s" does not refer to a path in a package.';
    public const int CODE = 1775606411;

    #[Pure]
    public function __construct(
        public readonly string $path,
        ?Throwable $previous = null,
    ) {
        parent::__construct(sprintf(self::MESSAGE, $this->path), self::CODE, $previous);
    }
}

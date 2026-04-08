<?php

namespace VerteXVaaR\BlueFoundation\Exception;

use JetBrains\PhpStorm\Pure;
use Throwable;
use VerteXVaaR\BlueSprints\BluesprintsException;

use function sprintf;

class PackageNotFoundException extends BluesprintsException
{
    private const string MESSAGE = 'The package "%s" does not exist.';
    public const int CODE = 1775606412;

    #[Pure]
    public function __construct(
        public readonly string $packageName,
        ?Throwable $previous = null,
    ) {
        parent::__construct(sprintf(self::MESSAGE, $this->packageName), self::CODE, $previous);
    }
}

<?php

namespace VerteXVaaR\BlueSeed\Exception;

use Exception;
use JetBrains\PhpStorm\Pure;

use function sprintf;

class SeederNotFoundException extends Exception
{
    public const int CODE = 1775652995;
    public const string MESSAGE = 'The seeder "%s" defines a dependency "%s" which does not exist.';

    #[Pure]
    public function __construct(
        public readonly string $identifier,
        public readonly string $dependency,
    ) {
        parent::__construct(sprintf(self::MESSAGE, $this->identifier, $this->dependency), self::CODE);
    }
}

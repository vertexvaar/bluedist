<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Environment;

use JetBrains\PhpStorm\Pure;

use function str_starts_with;
use function strtolower;

enum Context
{
    case Production;
    case Development;

    #[Pure]
    public static function fromString(string $context): Context
    {
        if (str_starts_with(strtolower($context), 'dev')) {
            return self::Development;
        }
        return self::Production;
    }
}

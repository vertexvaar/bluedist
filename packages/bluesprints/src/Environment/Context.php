<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Environment;

use function str_starts_with;
use function strtolower;

enum Context
{
    case Production;
    case Testing;
    case Development;

    public static function fromString(string $context): Context
    {
        if (str_starts_with(strtolower($context), 'dev')) {
            return self::Development;
        }
        if (str_starts_with(strtolower($context), 'test')) {
            return self::Testing;
        }
        return self::Production;
    }
}
